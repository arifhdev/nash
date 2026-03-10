<?php

namespace Database\Seeders;

use App\Models\Dealer;
use App\Models\MainDealer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DealerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Tabel Dulu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Dealer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $filePath = database_path('csv/dealers.csv');

        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Lewati Header
        fgetcsv($file, 2000, ';'); 

        $count = 0;
        
        // Perhatikan parameter ke-3 adalah titik koma (;)
        while (($row = fgetcsv($file, 2000, ';')) !== false) {
            
            // Mapping Kolom (Index array mulai dari 0)
            // 0 = No
            // 1 = Kode Dealer
            // 2 = Nama Dealer
            // 3 = Kode Main Dealer
            
            $kodeDealer     = $row[1] ?? null;
            $namaDealer     = $row[2] ?? null;
            $kodeMainDealer = $row[3] ?? null;

            if ($kodeDealer && $kodeMainDealer) {
                // Cari ID Main Dealer (Trim spasi jaga-jaga)
                $mainDealer = MainDealer::where('code', trim($kodeMainDealer))->first();

                if ($mainDealer) {
                    Dealer::create([
                        'main_dealer_id' => $mainDealer->id,
                        'name'           => trim($namaDealer), // Bersihkan spasi di nama
                        'code'           => trim($kodeDealer),
                        'address'        => null,
                    ]);
                    $count++;
                } else {
                    $this->command->warn("Main Dealer '$kodeMainDealer' tidak ditemukan untuk '$namaDealer'");
                }
            }
        }

        fclose($file);
        $this->command->info("Berhasil mengimpor $count dealer!");
    }
}