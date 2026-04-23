<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Division extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the positions associated with the division.
     * * Relasi ini memungkinkan kamu melihat semua jabatan 
     * (Manager, Staff, dll) yang terikat pada divisi ini.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}