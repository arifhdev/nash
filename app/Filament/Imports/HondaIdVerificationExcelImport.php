<?php

namespace App\Filament\Imports;

use App\Models\HondaIdStaging;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;
use Illuminate\Contracts\Queue\ShouldQueue;

class HondaIdVerificationExcelImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, ShouldQueue, WithEvents
{
    public $importId;

    public function __construct($importId = null)
    {
        $this->importId = $importId;
    }

    public function registerEvents(): array
    {
        return [
            // 1. SEBELUM IMPORT: Bersihkan Staging
            BeforeImport::class => function (BeforeImport $event) {
                DB::table('honda_id_stagings')->truncate();
            },

            // 2. SETELAH IMPORT SELESAI: Waktunya Sync dari Staging ke Tabel Utama
            AfterImport::class => function (AfterImport $event) {
                
                // A. Insert Jabatan baru (Aman karena tabel positions sudah di-UNIQUE)
                DB::statement("
                    INSERT IGNORE INTO positions (name, `group`, created_at, updated_at)
                    SELECT DISTINCT jabatan, `group`, NOW(), NOW() 
                    FROM honda_id_stagings 
                    WHERE jabatan IS NOT NULL AND jabatan != ''
                ");

                // B. Matikan status (is_active = 0) untuk data yang tidak ada di Excel
                DB::statement("
                    UPDATE honda_id_verifications 
                    SET is_active = 0, updated_at = NOW()
                    WHERE honda_id NOT IN (SELECT honda_id FROM honda_id_stagings)
                ");

                // C. UPSERT ke tabel Utama (Termasuk kolom NAME)
                DB::statement("
                    INSERT INTO honda_id_verifications (honda_id, name, main_dealer_id, dealer_id, position_id, is_active, created_at, updated_at)
                    SELECT 
                        s.honda_id,
                        s.name,
                        md.id as main_dealer_id,
                        d.id as dealer_id,
                        p.id as position_id,
                        1 as is_active,
                        NOW(), NOW()
                    FROM honda_id_stagings s
                    LEFT JOIN main_dealers md ON md.code = s.md_code
                    LEFT JOIN dealers d ON d.code = s.dealer_code
                    LEFT JOIN positions p ON p.name = s.jabatan AND (p.group = s.group OR (p.group IS NULL AND s.group IS NULL))
                    ON DUPLICATE KEY UPDATE
                        name = VALUES(name),
                        main_dealer_id = VALUES(main_dealer_id),
                        dealer_id = VALUES(dealer_id),
                        position_id = VALUES(position_id),
                        is_active = 1,
                        updated_at = NOW()
                ");

                // D. Update Progress di Log
                if ($this->importId) {
                    $totalStaging = DB::table('honda_id_stagings')->count();
                    DB::table('imports')->where('id', $this->importId)->update([
                        'total_rows' => $totalStaging,
                        'processed_rows' => $totalStaging,
                        'successful_rows' => $totalStaging,
                        'completed_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            },
        ];
    }

    public function model(array $row)
    {
        if (empty($row['honda_id'])) return null;

        // DI SINI KUNCINYA: Jangan panggil Create/Update DB di sini!
        // Cukup return Model agar di-batching secara masal ke Staging.
        return new HondaIdStaging([
            'honda_id'    => trim((string)$row['honda_id']),
            'name'        => trim((string)($row['name'] ?? $row['nama'] ?? '')),
            'md_code'     => trim((string)($row['md_code'] ?? '')),
            'dealer_code' => trim((string)($row['dealer_code'] ?? '')),
            'jabatan'     => trim((string)($row['jabatan'] ?? '')),
            'group'       => trim((string)($row['group'] ?? '')),
        ]);
    }

    // Setting gigi yang aman untuk server standar
    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}