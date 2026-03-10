<?php

namespace App\Models;

use App\Enums\MainDealerGroup; // Import Enum
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainDealer extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'group' => MainDealerGroup::class, // Auto convert string database ke Enum PHP
    ];

    public function dealers()
    {
        return $this->hasMany(Dealer::class);
    }
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Cek apakah Main Dealer ini adalah Head Office (Pusat)
     * Kriterianya: Kode berakhiran '-HO' dan punya kolom group
     */
    public function isHeadOffice(): bool
{
    // Gunakan strtoupper untuk jaga-jaga kode di DB ada yang huruf kecil
    return str_ends_with(strtoupper($this->code), '-HO') && !empty($this->group);
}
}