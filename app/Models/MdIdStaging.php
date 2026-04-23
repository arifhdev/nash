<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MdIdStaging extends Model
{
    use HasFactory;

    protected $table = 'md_id_stagings';

    protected $fillable = [
        'md_id', 
        'name',
        'divisi',   // Nyimpen kode divisi dari Excel
        'jabatan'   // Nyimpen nama jabatan dari Excel
    ];
}