<?php

namespace App\Filament\Imports;

use App\Models\MainDealer;
use App\Models\TrainerIdStaging; 
use App\Models\TrainerIdVerification;
use App\Models\Position; 
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport; 

class TrainerIdVerificationExcelImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $importId;
    protected $mainDealersCache;

    public function __construct($importId)
    {
        $this->importId = $importId;
        // Cache Main Dealer agar tidak query database berulang-ulang
        $this->mainDealersCache = MainDealer::pluck('id', 'code')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                // Bersihkan data temp lama
                TrainerIdStaging::truncate();
            },

            AfterImport::class => function(AfterImport $event) {
                // Sinkronisasi status aktif: yang tidak ada di excel terbaru jadi non-aktif
                TrainerIdVerification::whereNotIn('trainer_id', TrainerIdStaging::pluck('trainer_id'))
                    ->update(['is_active' => false]);

                if ($this->importId) {
                    $importLog = Import::find($this->importId);
                    if ($importLog) {
                        $importLog->update([
                            'completed_at' => now(),
                            'successful_rows' => $importLog->processed_rows, 
                        ]);
                    }
                }
            },
        ];
    }

    public function model(array $row)
    {
        $trainerId      = isset($row['trainer_id']) ? trim((string)$row['trainer_id']) : null;
        $mainDealerCode = isset($row['main_dealer_code']) ? trim((string)$row['main_dealer_code']) : null;
        $name           = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);
        $divisiCode     = isset($row['divisi']) ? trim((string)$row['divisi']) : null; // Isinya CODE (HC3, LOGISTIC, dll)
        $jabatanName    = isset($row['jabatan']) ? trim((string)$row['jabatan']) : (isset($row['position']) ? trim((string)$row['position']) : null);

        if (empty($trainerId) || empty($mainDealerCode)) {
            return null;
        }

        // 1. Lookup Main Dealer ID
        $mainDealerId = $this->mainDealersCache[$mainDealerCode] ?? null;

        // 2. Lookup Position ID berdasarkan Nama Jabatan & Code Divisi (user_type: main_dealer)
        // Trainer biasanya bernaung di bawah Main Dealer
        $positionId = null;
        if (!empty($divisiCode) && !empty($jabatanName)) {
            $position = Position::where('user_type', 'main_dealer') // Atau 'trainer' jika kamu buat tipe khusus
                ->where('name', $jabatanName)
                ->whereHas('division', function ($query) use ($divisiCode) {
                    $query->where('code', $divisiCode);
                })
                ->first();

            if ($position) {
                $positionId = $position->id;
            }
        }

        // 3. Simpan ke Staging (Temp) untuk record asli excel
        TrainerIdStaging::create([
            'trainer_id' => $trainerId,
            'name'       => $name,
            'divisi'     => $divisiCode,
            'jabatan'    => $jabatanName,
        ]);

        // 4. Update atau Buat di Tabel Whitelist Utama
        if ($mainDealerId) {
            TrainerIdVerification::updateOrCreate(
                ['trainer_id' => $trainerId],
                [
                    'main_dealer_id' => $mainDealerId,
                    'name'           => $name,
                    'position_id'    => $positionId, // Simpan ID Relasi
                    'is_active'      => true,
                    'updated_at'     => now(),
                ]
            );
        }

        // Update progress log
        $importLog = Import::find($this->importId);
        $importLog?->increment('processed_rows');

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}