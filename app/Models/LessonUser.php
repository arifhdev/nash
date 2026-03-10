<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LessonUser extends Pivot
{
    // Arahkan ke tabel pivot
    protected $table = 'lesson_user';

    // Agar Filament bisa membaca primary key (jika ada kolom id di pivot)
    public $incrementing = true; 

    protected $casts = [
        'started_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}