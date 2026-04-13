<?php

namespace App\Filament\Resources\PointHistoryResource\Pages;

use App\Filament\Resources\PointHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPointHistory extends EditRecord
{
    protected static string $resource = PointHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
