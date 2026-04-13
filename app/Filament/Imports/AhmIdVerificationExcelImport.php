<?php

namespace App\Filament\Imports; 

use App\Models\AhmIdStaging;
use App\Models\AhmIdVerification;
use App\Models\Position; 
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue; // 1. KEMBALIKAN IMPORT INI
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport; 

// 2. KEMBALIKAN ShouldQueue DI SINI
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
                AhmIdStaging::truncate();
                AhmIdVerification::query()->update(['is_active' => false]);
            },

            // Event ini yang akan mencegah UI loading selamanya
            AfterImport::class => function(AfterImport $event) {
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
        $ahmId = isset($row['ahm_id']) ? trim((string)$row['ahm_id']) : null;
        $name = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);
        $divisi = isset($row['divisi']) ? trim((string)$row['divisi']) : null;
        $jabatan = isset($row['jabatan']) ? trim((string)$row['jabatan']) : (isset($row['position']) ? trim((string)$row['position']) : null);

        if (empty($ahmId)) {
            return null;
        }

        $positionId = null;
        if (!empty($divisi) && !empty($jabatan)) {
            $position = Position::where('user_type', 'ahm')
                ->where('divisi', $divisi)
                ->where('name', $jabatan)
                ->first();

            if ($position) {
                $positionId = $position->id;
            }
        }

        AhmIdStaging::create([
            'ahm_id'  => $ahmId,
            'name'    => $name,
            'divisi'  => $divisi,
            'jabatan' => $jabatan,
        ]);

        AhmIdVerification::updateOrCreate(
            ['ahm_id' => $ahmId],
            [
                'name'        => $name, 
                'position_id' => $positionId, 
                'is_active'   => true
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