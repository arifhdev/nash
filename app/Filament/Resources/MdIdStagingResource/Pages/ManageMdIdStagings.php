<?php

namespace App\Filament\Resources\MdIdStagingResource\Pages;

use App\Filament\Resources\MdIdStagingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMdIdStagings extends ManageRecords
{
    protected static string $resource = MdIdStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Dikosongkan karena data masuk murni dari Import Excel
        ];
    }
}