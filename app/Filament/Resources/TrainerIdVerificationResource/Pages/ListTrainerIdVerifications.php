<?php

namespace App\Filament\Resources\TrainerIdVerificationResource\Pages;

use App\Filament\Resources\TrainerIdVerificationResource;
use App\Filament\Imports\TrainerIdVerificationExcelImport; // Pastikan Importer-nya menunjuk ke Trainer
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Imports\Models\Import;

class ListTrainerIdVerifications extends ListRecords
{
    protected static string $resource = TrainerIdVerificationResource::class;

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
                        'file_name' => 'Trainer Excel Import - ' . now()->format('H:i'), // Ubah nama log-nya
                        'file_path' => $data['file'],
                        'importer' => TrainerIdVerificationExcelImport::class, // Sesuaikan dengan class Importer Trainer
                        'total_rows' => 0, 
                        'processed_rows' => 0,
                    ]);

                    // Eksekusi Queue Import
                    Excel::queueImport(
                        new TrainerIdVerificationExcelImport($importLog->id), 
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