<?php

namespace App\Filament\Resources\CustomIdVerificationResource\Pages;

use App\Filament\Resources\CustomIdVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomIdVerification extends EditRecord
{
    protected static string $resource = CustomIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
