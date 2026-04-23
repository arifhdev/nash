<?php

namespace App\Filament\Resources\MdIdVerificationResource\Pages;

use App\Filament\Resources\MdIdVerificationResource;
use App\Filament\Imports\MdIdVerificationExcelImport; // Pastikan Importer-nya menunjuk ke MD ID
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Imports\Models\Import;

class ListMdIdVerifications extends ListRecords
{
    protected static string $resource = MdIdVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Import Custom
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
                        'file_name' => 'MD ID Excel Import - ' . now()->format('H:i'), // Ubah nama log-nya
                        'file_path' => $data['file'],
                        'importer' => MdIdVerificationExcelImport::class, // Sesuaikan dengan class Importer MD ID
                        'total_rows' => 0, 
                        'processed_rows' => 0,
                    ]);

                    // Eksekusi Queue Import
                    Excel::queueImport(
                        new MdIdVerificationExcelImport($importLog->id), 
                        $data['file'],
                        'public'
                    );

                    Notification::make()
                        ->title('Import Dimulai')
                        ->body('Data sedang diproses di background. Pantau di menu Monitor.')
                        ->success()
                        ->send();
                }),
                
            // Tombol Tambah Bawaan Filament
            CreateAction::make()->label('Tambah Manual'),
        ];
    }
}