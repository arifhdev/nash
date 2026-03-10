<?php

namespace App\Filament\Resources\HondaIdVerificationResource\Pages;

use App\Filament\Resources\HondaIdVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHondaIdVerification extends EditRecord
{
    protected static string $resource = HondaIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
