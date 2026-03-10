<?php

namespace App\Filament\Imports; 

use App\Models\AhmIdStaging;
use App\Models\AhmIdVerification;
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class AhmIdVerificationExcelImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $importId;

    public function __construct($importId)
    {
        $this->importId = $importId;
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                // 1. Kosongkan staging sebelum mulai baru
                AhmIdStaging::truncate();
                // 2. Nonaktifkan semua AHM ID lama (Sync Logic)
                AhmIdVerification::query()->update(['is_active' => false]);
            },
        ];
    }

    public function model(array $row)
    {
        $ahmId = isset($row['ahm_id']) ? trim((string)$row['ahm_id']) : null;
        
        // Cek header Excel untuk nama (Bisa sesuaikan dengan format template Excel kamu)
        // Disini saya buat antisipasi jika headernya bernama 'name' atau 'nama'
        $name = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);

        if (empty($ahmId)) {
            return null;
        }

        // 3. Simpan ke Monitor Temp (Staging) beserta nama
        AhmIdStaging::create([
            'ahm_id' => $ahmId,
            'name'   => $name, // Data baru masuk sini
        ]);

        // 4. Sinkron ke Whitelist Utama (Set Active) beserta nama
        AhmIdVerification::updateOrCreate(
            ['ahm_id' => $ahmId],
            [
                'name'      => $name, // Data baru masuk sini
                'is_active' => true
            ]
        );

        // Update progress log di dashboard
        $importLog = Import::find($this->importId);
        $importLog?->increment('processed_rows');

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}