<?php

namespace App\Filament\Imports;

use App\Models\MainDealer; 
use App\Models\MdIdStaging;
use App\Models\MdIdVerification;
use App\Models\Position; 
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport;

class MdIdVerificationExcelImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $importId;
    protected $mainDealersCache;

    public function __construct($importId)
    {
        $this->importId = $importId;
        // Cache kode MD untuk performa biar nggak query terus di dalam loop
        $this->mainDealersCache = MainDealer::pluck('id', 'code')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                MdIdStaging::truncate();
                // is_active jangan dimatikan di sini biar nggak ada downtime
            },

            AfterImport::class => function(AfterImport $event) {
                // Matikan data MD yang sudah tidak ada di list Excel terbaru
                MdIdVerification::whereNotIn('md_id', MdIdStaging::pluck('md_id'))->update(['is_active' => false]);

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
        $mdId           = isset($row['md_id']) ? trim((string)$row['md_id']) : (isset($row['custom_id']) ? trim((string)$row['custom_id']) : null);
        $mainDealerCode = isset($row['main_dealer_code']) ? trim((string)$row['main_dealer_code']) : null;
        $name           = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);
        $divisiCode     = isset($row['divisi']) ? trim((string)$row['divisi']) : null; // Isinya CODE (HC3, LOGISTIC, dll)
        $jabatanName    = isset($row['jabatan']) ? trim((string)$row['jabatan']) : (isset($row['position']) ? trim((string)$row['position']) : null);

        if (empty($mdId) || empty($mainDealerCode)) {
            return null;
        }

        // 1. Lookup Main Dealer ID
        $mainDealerId = $this->mainDealersCache[$mainDealerCode] ?? null;

        // 2. Lookup Position ID berdasarkan Nama Jabatan & Code Divisi
        $positionId = null;
        if (!empty($divisiCode) && !empty($jabatanName)) {
            $position = Position::where('user_type', 'main_dealer')
                ->where('name', $jabatanName)
                ->whereHas('division', function ($query) use ($divisiCode) {
                    $query->where('code', $divisiCode);
                })
                ->first();

            if ($position) {
                $positionId = $position->id;
            }
        }

        // Simpan ke Staging untuk tracking (Data mentah Excel)
        MdIdStaging::create([
            'md_id'   => $mdId,
            'name'    => $mainDealerId ? $name : ($name . ' (ERROR: Kode MD ' . $mainDealerCode . ' Tidak Terdaftar)'),
            'divisi'  => $divisiCode,
            'jabatan' => $jabatanName,
        ]);

        // 3. Upsert ke Tabel Whitelist MD
        if ($mainDealerId) {
            MdIdVerification::updateOrCreate(
                ['md_id' => $mdId],
                [
                    'main_dealer_id' => $mainDealerId,
                    'name'           => $name,
                    'position_id'    => $positionId, // Pakai ID, bukan string lagi
                    'is_active'      => true,
                    'updated_at'     => now(),
                ]
            );
        }

        if ($this->importId) {
            $importLog = Import::find($this->importId);
            $importLog?->increment('processed_rows');
        }

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}