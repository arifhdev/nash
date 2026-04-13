<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Override form actions di sini
    protected function getFormActions(): array
    {
        return [
            // 1. Tombol Save bawaan Filament
            $this->getSaveFormAction(),

            // 2. Tombol Save & Close
            Actions\Action::make('save_and_close')
                ->label('Save & Close')
                ->color('gray') // Ubah warna sesuai selera, misal: 'info', 'success', dll
                ->action(function () {
                    // Simpan data, tapi matikan redirect default bawaan save()
                    $this->save(shouldRedirect: false); 
                    
                    // Redirect manual ke halaman Index (Daftar Data)
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // 3. Tombol Save & Create New
            Actions\Action::make('save_and_create')
                ->label('Save & Create New')
                ->color('gray')
                ->action(function () {
                    // Simpan data, matikan redirect default
                    $this->save(shouldRedirect: false);
                    
                    // Redirect manual ke halaman Create
                    $this->redirect($this->getResource()::getUrl('create'));
                }),

            // 4. Tombol Close (menggunakan tombol Cancel bawaan tapi labelnya diubah)
            $this->getCancelFormAction()
                ->label('Close'),
        ];
    }
}