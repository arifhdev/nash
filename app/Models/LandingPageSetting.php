<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LandingPageSetting extends Model
{
    use HasFactory;

    /**
     * Nama tabel di database (opsional jika sudah jamak/plural)
     */
    protected $table = 'landing_page_settings';

    /**
     * Kolom yang boleh diisi (Mass Assignment)
     */
    protected $fillable = [
        'key',
        'payload',
    ];

    /**
     * Casting kolom payload ke Array agar otomatis jadi JSON saat simpan/ambil
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }
}