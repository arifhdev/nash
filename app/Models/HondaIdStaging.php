<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HondaIdStaging extends Model
{
    public $timestamps = false; // Set true jika migration pakai timestamps(), atau false jika tidak.
    protected $guarded = [];
    protected $primaryKey = 'honda_id';
    protected $keyType = 'string';
    public $incrementing = false;
}