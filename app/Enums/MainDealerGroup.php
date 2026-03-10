<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum MainDealerGroup: string implements HasLabel, HasColor
{
    case ASTRA_MOTOR = 'astra_motor';
    case CDN = 'cdn';
    case MPM = 'mpm';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ASTRA_MOTOR => 'Astra Motor Head Office',
            self::CDN => 'CDN (Capella Dinamik Nusantara)',
            self::MPM => 'MPM (Mitra Pinasthika Mulia)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ASTRA_MOTOR => 'info',    // Biru
            self::CDN => 'warning',         // Kuning/Oranye
            self::MPM => 'danger',          // Merah
        };
    }
}