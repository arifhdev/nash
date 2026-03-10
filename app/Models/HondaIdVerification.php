<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HondaIdVerification extends Model
{
    use HasFactory;

    protected $table = 'honda_id_verifications';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'has_account' => 'boolean',
    ];

    public function mainDealer(): BelongsTo
    {
        return $this->belongsTo(MainDealer::class, 'main_dealer_id');
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
}