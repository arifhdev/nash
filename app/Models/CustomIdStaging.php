<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomIdStaging extends Model
{
    // Menggunakan guarded kosong agar semua kolom (custom_id, name, dll) 
    // bisa diisi lewat Mass Assignment saat proses import Excel.
    protected $guarded = [];
}