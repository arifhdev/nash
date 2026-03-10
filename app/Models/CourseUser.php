<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CourseUser extends Pivot
{
    // Arahkan ke nama tabel pivot yang ada di database
    protected $table = 'course_user';

    // Agar bisa dibaca Filament sebagai primary key (jika tabel pivot punya kolom 'id')
    // Jika tidak punya kolom id, fitur Edit/Delete mungkin terbatas, tapi View aman.
    public $incrementing = true; 

    // Tambahkan fillable agar Anda bisa melakukan update / create data dengan aman
    protected $fillable = [
        'user_id',
        'course_id',
        'status',
        'progress_percent',
        'completed_at',
        'last_accessed_at', // Kolom baru untuk last access
    ];

    // Casting kolom tanggal agar otomatis menjadi object Carbon (DateTime)
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime', // Kolom baru untuk last access
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}