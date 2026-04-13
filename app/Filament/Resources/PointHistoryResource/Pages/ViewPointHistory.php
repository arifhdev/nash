<?php

namespace App\Filament\Resources\PointHistoryResource\Pages;

use App\Filament\Resources\PointHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPointHistory extends ViewRecord
{
    protected static string $resource = PointHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
