<?php

namespace App\Filament\Imports;

use App\Models\MainDealer;
use App\Models\TrainerIdStaging; 
use App\Models\TrainerIdVerification;
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class TrainerIdVerificationExcelImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $importId;
    protected $mainDealersCache;

    public function __construct($importId)
    {
        $this->importId = $importId;
        $this->mainDealersCache = MainDealer::pluck('id', 'code')->toArray();
    }

    public function registerEvents(): array
    {
        return [
            BeforeImport::class => function(BeforeImport $event) {
                TrainerIdStaging::truncate();
                TrainerIdVerification::query()->update(['is_active' => false]);
            },
        ];
    }

    public function model(array $row)
    {
        $trainerId = isset($row['trainer_id']) ? trim((string)$row['trainer_id']) : null;
        $mainDealerCode = isset($row['main_dealer_code']) ? trim((string)$row['main_dealer_code']) : null;
        $name = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);

        if (empty($trainerId) || empty($mainDealerCode)) {
            return null;
        }

        $mainDealerId = $this->mainDealersCache[$mainDealerCode] ?? null;

        if (!$mainDealerId) {
            return null;
        }

        TrainerIdStaging::create([
            'trainer_id' => $trainerId,
            'name' => $name,
        ]);

        TrainerIdVerification::updateOrCreate(
            ['trainer_id' => $trainerId],
            [
                'main_dealer_id' => $mainDealerId,
                'name' => $name,
                'is_active' => true,
            ]
        );

        $importLog = Import::find($this->importId);
        $importLog?->increment('processed_rows');

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}