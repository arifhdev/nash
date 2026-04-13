<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booklet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'pdf_file',
        'cover_image',
        'youtube_videos', // Ditambahkan
        'is_active',
    ];

    protected $casts = [
        'youtube_videos' => 'array', // Casting otomatis dari/ke JSON
    ];

    // Relasi Standar ke seluruh Jabatan (Bisa dipakai untuk query di frontend nanti)
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'booklet_position');
    }

    // --- 3 RELASI BARU UNTUK KEBUTUHAN FORM FILAMENT ---
    // Ditambahkan prefix "positions." agar terhindar dari error Column Ambiguous
    
    public function ahmPositions()
    {
        return $this->belongsToMany(Position::class, 'booklet_position')
                    ->where('positions.user_type', 'ahm');
    }

    public function mdPositions()
    {
        return $this->belongsToMany(Position::class, 'booklet_position')
                    ->where('positions.user_type', 'main_dealer');
    }

    public function dealerPositions()
    {
        return $this->belongsToMany(Position::class, 'booklet_position')
                    ->where('positions.user_type', 'dealer');
    }
    // --- END RELASI BARU ---

    // Relasi ke Main Dealer
    public function mainDealers()
    {
        return $this->belongsToMany(MainDealer::class);
    }
}