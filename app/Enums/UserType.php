<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum UserType: string implements HasLabel
{
    case AHM = 'ahm';
    case MAIN_DEALER = 'main_dealer';
    case DEALER = 'dealer';
    //case NON_DEALER = 'non_dealer';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::AHM => 'Karyawan AHM',
            self::MAIN_DEALER => 'Karyawan Main Dealer',
            self::DEALER => 'Karyawan Dealer',
            //self::NON_DEALER => 'Non Karyawan Dealer',
        };
    }
}