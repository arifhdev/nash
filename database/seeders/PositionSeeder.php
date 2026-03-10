<?php

namespace Database\Seeders;

use App\Models\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan tabel lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Position::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Lokasi File
        $filePath = database_path('csv/positions.csv');

        if (!file_exists($filePath)) {
            $this->command->error("File tidak ditemukan di: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        
        // Baca baris pertama untuk deteksi delimiter (koma atau titik koma)
        $firstLine = fgets($file);
        $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
        
        // Reset pointer file ke awal
        rewind($file);

        // Lewati Header
        fgetcsv($file, 2000, $delimiter); 

        $count = 0;
        while (($row = fgetcsv($file, 2000, $delimiter)) !== false) {
            // Mapping Kolom (Sesuaikan urutan di CSV Anda)
            // Asumsi: 
            // Kolom 0 = Jabatan
            // Kolom 1 = Group
            // Kolom 2 = Level
            
            $jabatan = $row[0] ?? null;
            $group   = $row[1] ?? null;
            $level   = $row[2] ?? null;

            if ($jabatan) {
                Position::create([
                    'name'  => trim($jabatan),
                    'group' => trim($group),
                    'level' => trim($level),
                ]);
                $count++;
            }
        }

        fclose($file);
        $this->command->info("Sukses! $count jabatan berhasil diimpor.");
    }
}