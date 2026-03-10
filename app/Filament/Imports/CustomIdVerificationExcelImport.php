<?php

namespace App\Filament\Imports;

use App\Models\MainDealer; 
use App\Models\CustomIdStaging;
use App\Models\CustomIdVerification;
use Filament\Actions\Imports\Models\Import;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\BeforeImport;

class CustomIdVerificationExcelImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
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
                // 1. Bersihkan staging agar monitor temp akurat
                CustomIdStaging::truncate();
                
                // 2. Set semua ke non-aktif (Logika Sync: yang tidak ada di Excel jadi tidak aktif)
                CustomIdVerification::query()->update(['is_active' => false]);
            },
        ];
    }

    public function model(array $row)
    {
        // 3. Ambil data dari baris Excel
        $customId       = isset($row['custom_id']) ? trim((string)$row['custom_id']) : null;
        $mainDealerCode = isset($row['main_dealer_code']) ? trim((string)$row['main_dealer_code']) : null;
        $name           = isset($row['name']) ? trim((string)$row['name']) : (isset($row['nama']) ? trim((string)$row['nama']) : null);

        // 4. Validasi dasar: Skip baris jika kolom krusial kosong
        if (empty($customId) || empty($mainDealerCode)) {
            return null;
        }

        // 5. Cari data Main Dealer berdasarkan kode
        $mainDealer = MainDealer::where('code', $mainDealerCode)->first();

        // 6. Simpan ke Monitor Temp (Staging) 
        // Tetap simpan ke staging walaupun MD tidak ketemu agar admin bisa cek error di Monitor
        CustomIdStaging::create([
            'custom_id' => $customId,
            'name'      => $mainDealer ? $name : ($name . ' (ERROR: Kode MD ' . $mainDealerCode . ' Tidak Terdaftar)'),
        ]);

        // 7. Jika Main Dealer ditemukan, lakukan sinkronisasi ke tabel utama
        if ($mainDealer) {
            CustomIdVerification::updateOrCreate(
                ['custom_id' => $customId],
                [
                    'main_dealer_id' => $mainDealer->id,
                    'name'           => $name,
                    'is_active'      => true,
                ]
            );
        }

        // 8. Update progress di dashboard Filament
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