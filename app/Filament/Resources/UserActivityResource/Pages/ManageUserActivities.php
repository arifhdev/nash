<?php

namespace App\Filament\Resources\UserActivityResource\Pages;

use App\Filament\Resources\UserActivityResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageUserActivities extends ManageRecords
{
    protected static string $resource = UserActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
