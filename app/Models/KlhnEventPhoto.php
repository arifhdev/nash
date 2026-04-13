<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlhnEventPhoto extends Model
{
    protected $fillable = ['klhn_event_id', 'image', 'caption'];

    public function event()
    {
        return $this->belongsTo(KlhnEvent::class, 'klhn_event_id');
    }
}