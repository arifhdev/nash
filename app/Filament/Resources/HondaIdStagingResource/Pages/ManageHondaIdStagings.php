<?php

namespace App\Filament\Resources\HondaIdStagingResource\Pages;

use App\Filament\Resources\HondaIdStagingResource;
use Filament\Resources\Pages\ManageRecords;

class ManageHondaIdStagings extends ManageRecords
{
    protected static string $resource = HondaIdStagingResource::class;

    // Dikosongkan agar tombol Create/New hilang
    protected function getHeaderActions(): array
    {
        return [];
    }
}