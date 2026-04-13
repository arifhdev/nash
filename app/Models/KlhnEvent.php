<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlhnEvent extends Model
{
    protected $fillable = ['title', 'event_date', 'description']; // Hapus 'gallery'
    
    protected $casts = [
        'event_date' => 'date',
    ];

    // Tambahkan relasi ini
    public function photos()
    {
        return $this->hasMany(KlhnEventPhoto::class);
    }
}