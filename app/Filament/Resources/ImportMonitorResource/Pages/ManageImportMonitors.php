<?php

namespace App\Filament\Resources\ImportMonitorResource\Pages;

use App\Filament\Resources\ImportMonitorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageImportMonitors extends ManageRecords
{
    protected static string $resource = ImportMonitorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Kosongkan agar tidak ada tombol "New Import Monitor"
        ];
    }
}