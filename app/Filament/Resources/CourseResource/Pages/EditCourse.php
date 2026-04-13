<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Filament\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCourse extends EditRecord
{
    protected static string $resource = CourseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(), // Opsional, tombol View di pojok kanan atas
            Actions\DeleteAction::make(),
        ];
    }

    // Override form actions di sini agar susunan tombol di bawah form sesuai
    protected function getFormActions(): array
    {
        return [
            // 1. Tombol Save bawaan Filament (untuk Save dan tetap di halaman Edit)
            $this->getSaveFormAction(),

            // 2. Tombol Save & Close
            Actions\Action::make('save_and_close')
                ->label('Save & Close')
                ->color('gray')
                ->action(function () {
                    // Simpan data, matikan redirect default
                    $this->save(shouldRedirect: false); 
                    
                    // Redirect manual ke halaman Index (Daftar Data Course)
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // 3. Tombol Save & Create New
            Actions\Action::make('save_and_create')
                ->label('Save & Create New')
                ->color('gray')
                ->action(function () {
                    // Simpan data, matikan redirect default
                    $this->save(shouldRedirect: false);
                    
                    // Redirect manual ke halaman Create Course
                    $this->redirect($this->getResource()::getUrl('create'));
                }),

            // 4. Tombol Close (menggunakan tombol Cancel bawaan tapi labelnya diubah)
            $this->getCancelFormAction()
                ->label('Close'),
        ];
    }
}