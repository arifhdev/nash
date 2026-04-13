<?php

namespace App\Filament\Resources\PointHistoryResource\Pages;

use App\Filament\Resources\PointHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPointHistories extends ListRecords
{
    protected static string $resource = PointHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
