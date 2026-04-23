<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerIdStaging extends Model
{
    use HasFactory;

    protected $table = 'trainer_id_stagings';

    protected $fillable = [
        'trainer_id', 
        'name',
        'divisi',   // Akan menyimpan CODE divisi dari Excel
        'jabatan'   // Akan menyimpan Nama jabatan dari Excel
    ];
}