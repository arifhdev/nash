<?php

namespace App\Filament\Resources\PdpResource\Pages;

use App\Filament\Resources\PdpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPdp extends EditRecord
{
    protected static string $resource = PdpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
