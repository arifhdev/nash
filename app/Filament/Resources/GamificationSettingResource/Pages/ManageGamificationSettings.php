<?php

namespace App\Filament\Resources\GamificationSettingResource\Pages;

use App\Filament\Resources\GamificationSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageGamificationSettings extends ManageRecords
{
    protected static string $resource = GamificationSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
