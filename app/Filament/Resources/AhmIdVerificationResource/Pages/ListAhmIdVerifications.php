<?php

namespace App\Filament\Resources\AhmIdVerificationResource\Pages;

use App\Filament\Resources\AhmIdVerificationResource;
use App\Filament\Imports\AhmIdVerificationExcelImport; // Namespace harus menunjuk ke folder Filament/Imports
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Imports\Models\Import;

class ListAhmIdVerifications extends ListRecords
{
    protected static string $resource = AhmIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_excel')
                ->label('Import Excel (.xlsx)')
                ->color('success')
                ->icon('heroicon-o-document-arrow-up')
                ->form([
                    FileUpload::make('file')
                        ->label('Pilih File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                        ->required()
                        ->directory('temp-imports')
                        ->disk('public'),
                ])
                ->action(function (array $data) {
                    // Buat log di tabel imports supaya sinkron dengan monitor
                    $importLog = Import::create([
                        'user_id' => auth()->id(),
                        'file_name' => 'AHM Excel Import - ' . now()->format('H:i'),
                        'file_path' => $data['file'],
                        'importer' => AhmIdVerificationExcelImport::class,
                        'total_rows' => 0, 
                        'processed_rows' => 0,
                    ]);

                    // Eksekusi Queue Import
                    Excel::queueImport(
                        new AhmIdVerificationExcelImport($importLog->id), 
                        $data['file'],
                        'public'
                    );

                    Notification::make()
                        ->title('Import Dimulai')
                        ->body('Data sedang diproses di background. Pantau di menu Monitor.')
                        ->success()
                        ->send();
                }),
                
            \Filament\Actions\CreateAction::make()->label('Tambah Manual'),
        ];
    }
}