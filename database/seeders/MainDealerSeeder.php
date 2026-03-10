<?php

namespace Database\Seeders;

use App\Models\MainDealer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainDealerSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel agar tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        MainDealer::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $data = [
            // SUMATERA
            ['code' => 'B10', 'name' => 'INDAKO TRADING COY', 'region' => 'SUMATERA'],
            ['code' => 'B3Z', 'name' => 'CAPELLA DINAMIK NUSANTARA - ACEH', 'region' => 'SUMATERA'],
            ['code' => 'C10', 'name' => 'MENARA AGUNG', 'region' => 'SUMATERA'],
            ['code' => 'C3Z', 'name' => 'HAYATI PRATAMA MANDIRI', 'region' => 'SUMATERA'],
            ['code' => 'D2Z', 'name' => 'CAPELLA DINAMIK NUSANTARA - RIAU DARATAN', 'region' => 'SUMATERA'],
            ['code' => 'D3Z', 'name' => 'CAPELLA DINAMIK NUSANTARA - KEPULAUAN RIAU', 'region' => 'SUMATERA'],
            ['code' => 'E20', 'name' => 'SINAR SENTOSA PRIMATAMA', 'region' => 'SUMATERA'],
            ['code' => 'G01', 'name' => 'ASTRA MOTOR SUMATRA SELATAN', 'region' => 'SUMATERA'],
            ['code' => 'G02', 'name' => 'ASTRA MOTOR BENGKULU', 'region' => 'SUMATERA'],
            ['code' => 'G5Z', 'name' => 'ASIA SURYA PERKASA', 'region' => 'SUMATERA'],
            ['code' => 'H2Z', 'name' => 'TUNAS DWIPA MATRA', 'region' => 'SUMATERA'],

            // JAWA
            ['code' => 'I01', 'name' => 'ASTRA MOTOR JAKARTA', 'region' => 'JAWA'],
            ['code' => 'I3Z', 'name' => 'WAHANA MAKMUR SEJATI', 'region' => 'JAWA'],
            ['code' => 'J10', 'name' => 'DAYA ADICIPTA MOTORA', 'region' => 'JAWA'],
            ['code' => 'J20', 'name' => 'MITRA SENDANG KEMAKMURAN', 'region' => 'JAWA'],
            ['code' => 'K0Z', 'name' => 'ASTRA MOTOR JAWA TENGAH', 'region' => 'JAWA'],
            ['code' => 'L01', 'name' => 'ASTRA MOTOR DIY', 'region' => 'JAWA'],
            ['code' => 'M2Z', 'name' => 'MITRA PINASTHIKA MULIA - SBY', 'region' => 'JAWA'],
            ['code' => 'M3Z', 'name' => 'MITRA PINASTHIKA MULIA - MLG', 'region' => 'JAWA'],
            ['code' => 'N01', 'name' => 'ASTRA MOTOR BALI', 'region' => 'JAWA'],
            ['code' => 'N02', 'name' => 'ASTRA MOTOR NTB', 'region' => 'JAWA'],

            // KASULPAP (Kalimantan, Sulawesi, Papua)
            ['code' => 'Q01', 'name' => 'ASTRA MOTOR KALBAR', 'region' => 'KASULPAP'],
            ['code' => 'R4Z', 'name' => 'ASTRA MOTOR KALTIM-1 ( BALIKPAPAN )', 'region' => 'KASULPAP'],
            ['code' => 'R5Z', 'name' => 'ASTRA MOTOR KALTIM-2 ( SAMARINDA )', 'region' => 'KASULPAP'],
            ['code' => 'T10', 'name' => 'TRIO MOTOR', 'region' => 'KASULPAP'],
            ['code' => 'U10', 'name' => 'DAYA ADICIPTA WISESA', 'region' => 'KASULPAP'],
            ['code' => 'V2Z', 'name' => 'ANUGERAH PERDANA', 'region' => 'KASULPAP'],
            ['code' => 'W01', 'name' => 'ASTRA MOTOR SULAWESI SELATAN', 'region' => 'KASULPAP'],
            ['code' => 'Z11', 'name' => 'ASTRA MOTOR PAPUA', 'region' => 'KASULPAP'],

            // HEAD OFFICE / OTHER
            ['code' => 'CDN-HO', 'name' => 'CAPELLA DINAMIK NUSANTARA HEAD OFFICE', 'region' => '0'],
            ['code' => 'ASMO-HO', 'name' => 'ASTRA MOTOR HEAD OFFICE', 'region' => '0'],
        ];

        foreach ($data as $item) {
            MainDealer::create($item);
        }
    }
}