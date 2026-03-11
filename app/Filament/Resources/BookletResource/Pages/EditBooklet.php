<?php

namespace App\Filament\Resources\BookletResource\Pages;

use App\Filament\Resources\BookletResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBooklet extends EditRecord
{
    protected static string $resource = BookletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
