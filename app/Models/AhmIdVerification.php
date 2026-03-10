<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AhmIdVerification extends Model
{
    protected $fillable = ['ahm_id', 'name', 'is_active', 'has_account'];
}