<?php

namespace App\Filament\Resources\LandingPageSettingResource\Pages;

use App\Filament\Resources\LandingPageSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLandingPageSettings extends ListRecords
{
    protected static string $resource = LandingPageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
