<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhmIdStaging extends Model
{
    protected $fillable = [
        'ahm_id', 
        'name',
        'divisi',   // Ditambahkan untuk menampung teks Divisi dari Excel
        'jabatan'   // Ditambahkan untuk menampung teks Jabatan dari Excel
    ];
}