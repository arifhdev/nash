<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dealer extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Relasi ke Main Dealer (Induk)
     */
    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class);
    }
    
    /**
     * Relasi ke Karyawan yang terdaftar di Dealer/AHASS ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}