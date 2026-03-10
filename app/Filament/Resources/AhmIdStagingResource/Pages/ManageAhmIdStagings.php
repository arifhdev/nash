<?php

namespace App\Filament\Resources\AhmIdStagingResource\Pages;

use App\Filament\Resources\AhmIdStagingResource;
use Filament\Resources\Pages\ManageRecords;

class ManageAhmIdStagings extends ManageRecords
{
    protected static string $resource = AhmIdStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}