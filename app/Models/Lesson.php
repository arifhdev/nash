<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $guarded = [];

    protected $casts = [
        'quiz_data' => 'array',
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