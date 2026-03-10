<?php

namespace App\Filament\Resources\CustomIdVerificationResource\Pages;

use App\Filament\Resources\CustomIdVerificationResource;
use App\Filament\Imports\CustomIdVerificationExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Imports\Models\Import;

class ListCustomIdVerifications extends ListRecords
{
    protected static string $resource = CustomIdVerificationResource::class;

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
                    // Buat log import agar muncul di monitor bawaan
                    $importLog = Import::create([
                        'user_id' => auth()->id(),
                        'file_name' => 'Custom ID Excel Import - ' . now()->format('H:i'),
                        'file_path' => $data['file'],
                        'importer' => CustomIdVerificationExcelImport::class,
                        'total_rows' => 0, 
                        'processed_rows' => 0,
                    ]);

                    // Jalankan Queue Import dengan Maatwebsite Excel
                    Excel::queueImport(
                        new CustomIdVerificationExcelImport($importLog->id), 
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