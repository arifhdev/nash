<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HondaIdStaging extends Model
{
    use HasFactory;

    protected $table = 'honda_id_stagings';

    protected $fillable = [
        'honda_id',
        'name',
        'md_code',
        'dealer_code',
        'jabatan',
        'divisi',
    ];
}