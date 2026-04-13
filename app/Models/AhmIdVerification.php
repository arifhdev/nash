<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AhmIdVerification extends Model
{
    protected $fillable = [
        'ahm_id', 
        'name', 
        'position_id', // Ditambahkan untuk menyimpan data relasi
        'is_active', 
        'has_account'
    ];

    /**
     * Relasi ke tabel positions
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}