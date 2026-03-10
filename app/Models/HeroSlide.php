<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'image',
        'image_mobile',
        'link',
        'is_active',
        'sort_order',
    ];
}