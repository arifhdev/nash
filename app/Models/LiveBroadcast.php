<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveBroadcast extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'youtube_url',
        'description',
        'status',
        'scheduled_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    /**
     * Accessor ajaib untuk mengambil 11 Karakter Video ID dari URL YouTube
     * Bisa baca format youtube.com, youtu.be, youtube.com/live, dll.
     */
    public function getYoutubeIdAttribute()
    {
        if (!$this->youtube_url) return null;
        
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/|youtube\.com/live/)([^"&?/\s]{11})%i', $this->youtube_url, $match);
        
        return $match[1] ?? null;
    }
}