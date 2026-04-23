<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AhmIdStaging extends Model
{
    use HasFactory;

    protected $table = 'ahm_id_stagings';

    protected $fillable = [
        'ahm_id', 
        'name',
        'divisi',  // Akan menyimpan CODE Divisi (MARKETING, LOG, dll)
        'jabatan'  // Akan menyimpan Nama Jabatan mentah
    ];
}