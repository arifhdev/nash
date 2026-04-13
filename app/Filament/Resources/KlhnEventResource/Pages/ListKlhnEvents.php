<?php

namespace App\Filament\Resources\KlhnEventResource\Pages;

use App\Filament\Resources\KlhnEventResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKlhnEvents extends ListRecords
{
    protected static string $resource = KlhnEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Event Baru') // Mengubah teks tombol di sini
                ->icon('heroicon-o-plus-circle'), // Opsional: tambah icon agar lebih manis
        ];
    }
}