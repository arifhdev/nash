<?php

namespace App\Filament\Resources\TrainerIdVerificationResource\Pages;

use App\Filament\Resources\TrainerIdVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainerIdVerification extends EditRecord
{
    protected static string $resource = TrainerIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
