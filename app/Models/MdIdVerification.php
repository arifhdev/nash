<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MdIdVerification extends Model
{
    use HasFactory;

    protected $table = 'md_id_verifications';

    protected $fillable = [
        'md_id',
        'main_dealer_id',
        'position_id', // Tambahkan ini
        'name',
        'divisi',      // Biarkan dulu untuk backup data lama
        'jabatan',     // Biarkan dulu untuk backup data lama
        'is_active',
        'has_account',
    ];

    /**
     * Casting data agar Laravel otomatis konversi tipe datanya.
     */
    protected $casts = [
        'is_active'   => 'boolean',
        'has_account' => 'boolean',
        'position_id' => 'integer',
        'main_dealer_id' => 'integer',
    ];

    /**
     * Relasi ke Main Dealer (Pusat Regional).
     */
    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class, 'main_dealer_id');
    }

    /**
     * Relasi ke Master Jabatan.
     * Lewat sini kita bisa narik data Divisi juga.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}