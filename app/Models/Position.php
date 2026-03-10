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
        'group',
        'level',
    ];

    /**
     * Relasi: Satu Jabatan bisa dimiliki oleh banyak User.
     * Contoh penggunaan: $position->users;
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'position_user');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_position');
    }
}