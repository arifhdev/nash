<?php

namespace App\Filament\Resources\DivisionResource\Pages;

use App\Filament\Resources\DivisionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDivisions extends ListRecords
{
    protected static string $resource = DivisionResource::class;

    /**
     * Mengatur Action di bagian Header (kanan atas) pada halaman List.
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Divisi Baru')
                ->icon('heroicon-o-plus-circle')
                ->modalHeading('Buat Master Divisi'),
        ];
    }

    /**
     * Opsional: Kamu bisa menambahkan fungsi getTitle() 
     * jika ingin mengubah judul halaman secara spesifik.
     */
    public function getTitle(): string 
    {
        return 'Daftar Divisi';
    }
}