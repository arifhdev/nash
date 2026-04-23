<?php

namespace App\Filament\Imports;

use App\Models\HondaIdStaging;
use Filament\Actions\Imports\Models\Import;
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
            BeforeImport::class => function (BeforeImport $event) {
                DB::table('honda_id_stagings')->truncate();
            },

            AfterImport::class => function (AfterImport $event) {
                
                // Transaksi dimatikan agar RAM server (yang sisa sedikit) tidak tercekik saat mengunci 90rb data sekaligus
                
                // A. INSERT JABATAN BARU
                // Karena data s.divisi sudah bersih dari PHP, kita panggil langsung namanya (tanpa UPPER/TRIM)
                DB::statement("
                    INSERT IGNORE INTO positions (name, division_id, user_type, created_at, updated_at)
                    SELECT DISTINCT s.jabatan, d.id, 'dealer', NOW(), NOW() 
                    FROM honda_id_stagings s
                    INNER JOIN divisions d ON d.code = s.divisi
                    WHERE s.jabatan IS NOT NULL AND s.jabatan != ''
                ");

                // B. NONAKTIFKAN DATA YANG TIDAK DIUPLOAD
                DB::statement("
                    UPDATE honda_id_verifications v
                    LEFT JOIN honda_id_stagings s ON v.honda_id = s.honda_id
                    SET v.is_active = 0, v.updated_at = NOW()
                    WHERE s.honda_id IS NULL AND v.is_active = 1
                ");

                // C. UPSERT DATA UTAMA (Sekarang Index bekerja 100% secara instan)
                DB::statement("
                    INSERT INTO honda_id_verifications (honda_id, name, main_dealer_id, dealer_id, position_id, is_active, created_at, updated_at)
                    SELECT 
                        s.honda_id,
                        s.name,
                        md.id as main_dealer_id,
                        d.id as dealer_id,
                        p.id as position_id,
                        1, 
                        NOW(), NOW()
                    FROM honda_id_stagings s
                    LEFT JOIN main_dealers md ON md.code = s.md_code
                    LEFT JOIN dealers d ON d.code = s.dealer_code
                    LEFT JOIN divisions div ON div.code = s.divisi
                    LEFT JOIN positions p ON p.name = s.jabatan 
                                         AND p.division_id = div.id 
                                         AND p.user_type = 'dealer'
                    ON DUPLICATE KEY UPDATE
                        name = VALUES(name),
                        main_dealer_id = VALUES(main_dealer_id),
                        dealer_id = VALUES(dealer_id),
                        position_id = VALUES(position_id),
                        is_active = 1,
                        updated_at = NOW()
                ");

                // D. Update Progress Filament
                if ($this->importId) {
                    $importLog = Import::find($this->importId);
                    if ($importLog) {
                        $totalStaging = DB::table('honda_id_stagings')->count();
                        $importLog->update([
                            'total_rows'      => $totalStaging,
                            'processed_rows'  => $totalStaging,
                            'successful_rows' => $totalStaging,
                            'completed_at'    => now(),
                        ]);
                    }
                }
            },
        ];
    }

    public function model(array $row)
    {
        if (empty($row['honda_id'])) return null;

        // PERBAIKAN KRUSIAL: Bersihkan string di sini, agar SQL Index tidak rusak!
        return new HondaIdStaging([
            'honda_id'    => trim((string)$row['honda_id']),
            'name'        => trim((string)($row['name'] ?? $row['nama'] ?? '')),
            'md_code'     => trim((string)($row['md_code'] ?? '')),
            'dealer_code' => trim((string)($row['dealer_code'] ?? '')),
            'jabatan'     => trim((string)($row['jabatan'] ?? '')),
            // Langsung paksakan jadi HURUF BESAR saat masuk ke staging
            'divisi'      => strtoupper(trim((string)($row['divisi'] ?? $row['group'] ?? ''))), 
        ]);
    }

    public function batchSize(): int { return 1000; }
    public function chunkSize(): int { return 1000; }
}