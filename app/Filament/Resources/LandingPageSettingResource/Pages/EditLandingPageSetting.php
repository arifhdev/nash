<?php

namespace App\Filament\Resources\LandingPageSettingResource\Pages;

use App\Filament\Resources\LandingPageSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLandingPageSetting extends EditRecord
{
    protected static string $resource = LandingPageSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
