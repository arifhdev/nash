<?php

namespace Database\Seeders;

use App\Models\HondaIdVerification;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class HondaIdVerificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = database_path('seeders/honda_id_verifications.csv');

        if (!File::exists($csvFile)) {
            $this->command->error("File CSV tidak ditemukan di: {$csvFile}");
            return;
        }

        $file = fopen($csvFile, 'r');
        
        // 1. Auto-detect delimiter (Koma atau Titik Koma)
        $firstLine = fgets($file);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        rewind($file); // Kembalikan pointer pembacaan ke baris pertama

        // 2. Ambil header dan bersihkan dari karakter tersembunyi/spasi
        $header = fgetcsv($file, 1000, $delimiter);
        $header = array_map(function($val) {
            return trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', strtolower($val)));
        }, $header);

        $count = 0;

        while (($row = fgetcsv($file, 1000, $delimiter)) !== false) {
            // Pastikan jumlah kolom sama
            if (count($header) === count($row)) {
                $data = array_combine($header, $row);

                $hondaId = isset($data['honda_id']) ? trim($data['honda_id']) : null;
                
                // Skip jika baris kosong
                if (empty($hondaId)) continue;

                // --- MAPPING SESUAI SCREENSHOT EXCEL ---
                // Di Excel: 'dealer_co' isinya W01, B10 (Ini adalah Main Dealer)
                // Di Excel: 'md_code' isinya 100009, 100030 (Ini adalah Dealer)
                
                $csvMainDealerCode = $data['dealer_co'] ?? $data['dealer_code'] ?? null;
                $csvDealerCode = $data['md_code'] ?? null;

                // A. Cari ID Main Dealer
                $mainDealerId = null;
                if (!empty(trim($csvMainDealerCode))) {
                    $md = MainDealer::where('code', trim($csvMainDealerCode))->first();
                    $mainDealerId = $md ? $md->id : null;
                }

                // B. Cari ID Dealer
                $dealerId = null;
                if (!empty(trim($csvDealerCode))) {
                    $dealer = Dealer::where('code', trim($csvDealerCode))->first();
                    $dealerId = $dealer ? $dealer->id : null;
                }

                // C. Translasi Nama Jabatan & Group ke ID
                $positionId = null;
                if (!empty($data['jabatan'])) {
                    $jabatanName = trim($data['jabatan']);
                    $groupName = !empty($data['group']) ? trim($data['group']) : null;

                    // Cari jabatan (cocokkan nama dan grupnya)
                    $query = Position::where('name', $jabatanName);
                    if ($groupName) {
                        $query->where('group', $groupName);
                    }
                    $position = $query->first();

                    // Jika belum ada di master data, buat otomatis
                    if (!$position) {
                        $position = Position::create([
                            'name' => $jabatanName,
                            'group' => $groupName,
                        ]);
                    }
                    $positionId = $position->id;
                }

                // D. Simpan ke Database
                HondaIdVerification::updateOrCreate(
                    ['honda_id' => $hondaId], // Acuan (tidak boleh ganda)
                    [
                        'is_active'      => true,  // Default aktif
                        'has_account'    => false, // Default belum daftar
                        'main_dealer_id' => $mainDealerId,
                        'dealer_id'      => $dealerId,
                        'position_id'    => $positionId,
                    ]
                );
                
                $count++;
            }
        }

        fclose($file);
        $this->command->info("Berhasil memproses {$count} data Honda ID Whitelist dari CSV.");
    }
}