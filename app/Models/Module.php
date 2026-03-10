<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Import this!
use Illuminate\Support\Str;

class Module extends Model
{
    protected $guarded = [];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted()
    {
        static::creating(function ($module) {
            if (empty($module->slug)) {
                $module->slug = Str::slug($module->name);
            }
        });
    }

    // Relationship to Lessons (Many-to-Many)
    public function lessons(): BelongsToMany
    {
        return $this->belongsToMany(Lesson::class)
                    ->withPivot('sort_order')
                    ->withTimestamps()
                    ->orderByPivot('sort_order');
    }

    // ADD THIS: Relationship to Courses (Many-to-Many Inverse)
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
                    ->withPivot('sort_order')
                    ->withTimestamps();
    }
}