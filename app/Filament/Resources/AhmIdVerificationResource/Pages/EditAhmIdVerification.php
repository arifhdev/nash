<?php

namespace App\Filament\Resources\AhmIdVerificationResource\Pages;

use App\Filament\Resources\AhmIdVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAhmIdVerification extends EditRecord
{
    protected static string $resource = AhmIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
