<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_checkin_points',
        'daily_checkin_xp',
    ];
}