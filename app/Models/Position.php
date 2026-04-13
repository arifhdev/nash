<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $fillable = [
        'name',
        'user_type', 
        'divisi',    
        'level',
    ];

    /**
     * PERBAIKAN: Relasi One-to-Many.
     * Satu Jabatan bisa dimiliki oleh banyak User.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'position_id');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_position');
    }
}