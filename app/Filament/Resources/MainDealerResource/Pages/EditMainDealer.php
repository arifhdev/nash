<?php

namespace App\Filament\Resources\MainDealerResource\Pages;

use App\Filament\Resources\MainDealerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainDealer extends EditRecord
{
    protected static string $resource = MainDealerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
