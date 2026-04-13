<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    // Menggunakan guarded kosong berarti semua kolom otomatis diizinkan untuk mass-assignment
    protected $guarded = [];

    protected $casts = [
        'quiz_data' => 'array',
        'quiz_display_count' => 'integer', // Pastikan kolom baru ini di-cast sebagai integer
        'is_free' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($lesson) {
            if (empty($lesson->slug)) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }

    // Relasi ke Modules
    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)->withPivot('sort_order')->withTimestamps();
    }
}