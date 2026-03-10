<?php

namespace App\Filament\Resources\PdpResource\Pages;

use App\Filament\Resources\PdpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPdps extends ListRecords
{
    protected static string $resource = PdpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
