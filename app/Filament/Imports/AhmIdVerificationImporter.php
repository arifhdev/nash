<?php

namespace App\Imports;

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
                // 1. Kosongkan staging
                AhmIdStaging::truncate();
                // 2. Set semua data utama jadi Inactive sebelum disinkronkan kembali
                AhmIdVerification::query()->update(['is_active' => false]);
            },
        ];
    }

    public function model(array $row)
    {
        $ahmId = trim($row['ahm_id'] ?? '');

        if (empty($ahmId)) {
            return null;
        }

        // 3. Masukkan ke Staging (Monitor)
        AhmIdStaging::create(['ahm_id' => $ahmId]);

        // 4. Sinkron ke tabel Utama (Update/Create dan aktifkan)
        AhmIdVerification::updateOrCreate(
            ['ahm_id' => $ahmId],
            ['is_active' => true]
        );

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