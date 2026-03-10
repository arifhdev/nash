<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomIdVerification extends Model
{
    use HasFactory;

    // Guarded kosong berarti semua kolom diizinkan untuk Mass Assignment.
    // Sangat aman selama kita pakai Filament yang inputnya sudah tervalidasi di Form Schema.
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'has_account' => 'boolean',
    ];

    /**
     * Relasi ke tabel main_dealers
     */
    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class);
    }
}