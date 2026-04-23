<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerIdVerification extends Model
{
    use HasFactory;

    protected $table = 'trainer_id_verifications';

    protected $fillable = [
        'trainer_id',
        'main_dealer_id',
        'position_id', // Tambahkan ini agar bisa menyimpan relasi jabatan
        'name',
        'divisi',      // Masih dipertahankan sementara sebagai backup data string
        'jabatan',     // Masih dipertahankan sementara sebagai backup data string
        'is_active',
        'has_account',
    ];

    /**
     * Casting data agar tipe datanya konsisten saat diproses.
     */
    protected $casts = [
        'is_active'      => 'boolean',
        'has_account'    => 'boolean',
        'position_id'    => 'integer',
        'main_dealer_id' => 'integer',
    ];

    /**
     * Relasi ke Main Dealer.
     */
    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class, 'main_dealer_id');
    }

    /**
     * Relasi ke Master Jabatan.
     * Melalui ini, kita bisa mengakses data Divisi (position->division->name).
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}