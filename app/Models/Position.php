<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'division_id', 
        'name',
        'user_type', 
        'divisi', // Segera hapus jika migrasi ID selesai
        'level',
    ];

    protected $casts = [
        'division_id' => 'integer',
    ];

    /**
     * Relasi ke Master Divisi.
     */
    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Relasi ke User (Karyawan).
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'position_id');
    }

    /**
     * Relasi ke Data Whitelist/Verifikasi.
     */
    public function hondaIdVerifications(): HasMany
    {
        return $this->hasMany(HondaIdVerification::class, 'position_id');
    }

    /**
     * Relasi Many-to-Many dengan Course.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_position');
    }
}