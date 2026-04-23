<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HondaIdVerification extends Model
{
    use HasFactory;

    protected $table = 'honda_id_verifications';

    protected $fillable = [
        'honda_id',
        'name',
        'main_dealer_id',
        'dealer_id',
        'position_id',
        'is_active',
        'has_account',
    ];

    /**
     * Casting data agar konsisten. 
     * Integer casting sangat penting untuk performa query relasi.
     */
    protected $casts = [
        'is_active'      => 'boolean',
        'has_account'    => 'boolean',
        'main_dealer_id' => 'integer',
        'dealer_id'      => 'integer',
        'position_id'    => 'integer',
    ];

    /**
     * Relasi ke Main Dealer.
     */
    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class, 'main_dealer_id');
    }

    /**
     * Relasi ke Dealer.
     */
    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    /**
     * Relasi ke Master Jabatan.
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}