<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use Filament\Actions;
use Filament\Forms; // <-- Tambahkan ini untuk komponen form FileUpload
use Filament\Resources\Pages\ListRecords;

class ListLessons extends ListRecords
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // --- TOMBOL IMPORT EXCEL DITAMBAHKAN DI SINI ---
            Actions\Action::make('import_excel')
                ->label('Import Excel')
                ->color('success')
                ->icon('heroicon-o-arrow-up-tray')
                ->form([
                    Forms\Components\FileUpload::make('file')
                        ->label('Upload File Excel (.xlsx / .csv)')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                            'application/vnd.ms-excel', // .xls
                            'text/csv', // .csv
                        ])
                        ->disk('public')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Ambil path file
                    $file = storage_path('app/public/' . $data['file']);
                    
                    // Jalankan import
                    \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\LessonsImport, $file);
                    
                    // Hapus file setelah import selesai
                    if (file_exists($file)) {
                        unlink($file);
                    }

                    // Notifikasi sukses
                    \Filament\Notifications\Notification::make()
                        ->title('Data Pelajaran berhasil diimport!')
                        ->success()
                        ->send();
                }),
            // ------------------------------------------------

            Actions\CreateAction::make(),
        ];
    }
}