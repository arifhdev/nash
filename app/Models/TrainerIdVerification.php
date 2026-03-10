<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainerIdVerification extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'has_account' => 'boolean',
    ];

    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class);
    }
}