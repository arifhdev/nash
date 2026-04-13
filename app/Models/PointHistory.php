<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class PointHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',       // Untuk mencatat Poin (Currency)
        'xp_amount',    // Untuk mencatat XP (Leaderboard)
        'description',
        'source_type',
        'source_id',
    ];

    /**
     * Relasi balik ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relation untuk mengetahui dari mana poin/xp ini berasal
     * (Misal: dari Course A, atau dari Daily Login)
     */
    public function source(): MorphTo
    {
        return $this->morphTo();
    }
}