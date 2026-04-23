<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AhmIdVerification extends Model
{
    use HasFactory;

    protected $table = 'ahm_id_verifications';

    protected $fillable = [
        'ahm_id', 
        'name', 
        'position_id', 
        'is_active', 
        'has_account'
    ];

    /**
     * Pastikan tipe data dikonversi otomatis oleh Eloquent.
     */
    protected $casts = [
        'is_active'   => 'boolean',
        'has_account' => 'boolean',
        'position_id' => 'integer',
    ];

    /**
     * Relasi ke tabel positions.
     * Digunakan untuk menarik data Jabatan dan Divisi (via Division).
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    /**
     * Opsional: Relasi ke User (Jika ahm_id dipakai di tabel users)
     * Ini berguna untuk mengecek secara otomatis apakah user sudah register.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ahm_id', 'ahm_id');
    }
}