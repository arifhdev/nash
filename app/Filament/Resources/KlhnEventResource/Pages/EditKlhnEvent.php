<?php

namespace App\Filament\Resources\KlhnEventResource\Pages;

use App\Filament\Resources\KlhnEventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKlhnEvent extends EditRecord
{
    protected static string $resource = KlhnEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
