<?php

namespace App\Filament\Resources\TrainerIdStagingResource\Pages;

use App\Filament\Resources\TrainerIdStagingResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTrainerIdStagings extends ManageRecords
{
    protected static string $resource = TrainerIdStagingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
