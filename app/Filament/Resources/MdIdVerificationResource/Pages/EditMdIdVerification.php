<?php

namespace App\Filament\Resources\MdIdVerificationResource\Pages;

use App\Filament\Resources\MdIdVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMdIdVerification extends EditRecord
{
    protected static string $resource = MdIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}