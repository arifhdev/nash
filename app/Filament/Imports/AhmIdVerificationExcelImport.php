<?php

namespace App\Filament\Imports; 

use App\Models\AhmIdStaging;
use App\Models\AhmIdVerification;
use App\Models\Position; 
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue; 
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\AfterImport; 

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
            // 1. Persiapan sebelum data masuk
            BeforeImport::class => function(BeforeImport $event) {
                AhmIdStaging::truncate();
                // Kita tidak matikan is_active di sini agar tidak ada downtime saat proses import berjalan
            },

            // 2. Finalisasi setelah semua chunk selesai
            AfterImport::class => function(AfterImport $event) {
                // Matikan data yang tidak ada di staging (Data lama yang sudah dihapus di Excel)
                AhmIdVerification::whereNotIn('ahm_id', AhmIdStaging::pluck('ahm_id'))->update(['is_active' => false]);

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
        $divisiCode = isset($row['divisi']) ? trim((string)$row['divisi']) : null; // Isinya sekarang CODE (MARKETING, LOGISTIC, dll)
        $jabatanName = isset($row['jabatan']) ? trim((string)$row['jabatan']) : (isset($row['position']) ? trim((string)$row['position']) : null);

        if (empty($ahmId)) {
            return null;
        }

        // KUNCINYA DI SINI: Cari Position berdasarkan Nama Jabatan DAN Code Divisinya
        $positionId = null;
        if (!empty($divisiCode) && !empty($jabatanName)) {
            $position = Position::where('user_type', 'ahm')
                ->where('name', $jabatanName)
                ->whereHas('division', function ($query) use ($divisiCode) {
                    $query->where('code', $divisiCode);
                })
                ->first();

            if ($position) {
                $positionId = $position->id;
            }
        }

        // Simpan ke Staging untuk tracking
        AhmIdStaging::create([
            'ahm_id'  => $ahmId,
            'name'    => $name,
            'divisi'  => $divisiCode,
            'jabatan' => $jabatanName,
        ]);

        // Upsert ke Tabel Utama
        AhmIdVerification::updateOrCreate(
            ['ahm_id' => $ahmId],
            [
                'name'        => $name, 
                'position_id' => $positionId, 
                'is_active'   => true, // Selalu aktifkan jika ada di Excel terbaru
                'updated_at'  => now(),
            ]
        );

        // Update progress monitor Filament
        $importLog = Import::find($this->importId);
        $importLog?->increment('processed_rows');

        return null;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}