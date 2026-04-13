<?php

namespace App\Filament\Resources\LiveBroadcastResource\Pages;

use App\Filament\Resources\LiveBroadcastResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLiveBroadcasts extends ListRecords
{
    protected static string $resource = LiveBroadcastResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
