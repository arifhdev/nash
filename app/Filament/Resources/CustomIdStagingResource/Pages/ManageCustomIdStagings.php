<?php

namespace App\Filament\Resources\CustomIdStagingResource\Pages;

use App\Filament\Resources\CustomIdStagingResource;
use Filament\Resources\Pages\ManageRecords;

class ManageCustomIdStagings extends ManageRecords
{
    protected static string $resource = CustomIdStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan agar tidak ada tombol Create
        ];
    }
}