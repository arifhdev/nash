<?php

namespace App\Filament\Resources\HondaIdVerificationResource\Pages;

use App\Filament\Resources\HondaIdVerificationResource;
use App\Filament\Imports\HondaIdVerificationExcelImport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Imports\Models\Import;

class ListHondaIdVerifications extends ListRecords
{
    protected static string $resource = HondaIdVerificationResource::class;

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
                        ->visibility('public'),
                ])
                ->action(function (array $data) {
                    // Buat log kosong dulu
                    $importLog = Import::create([
                        'user_id' => auth()->id(),
                        'file_name' => 'Excel Import - ' . now()->format('H:i'),
                        'file_path' => $data['file'],
                        'importer' => HondaIdVerificationExcelImport::class,
                        'total_rows' => 0, 
                        'processed_rows' => 0,
                    ]);

                    // Jalankan queue import
                    Excel::queueImport(
                        new HondaIdVerificationExcelImport($importLog->id), 
                        $data['file'],
                        'public'
                    );

                    Notification::make()
                        ->title('Import Dimulai')
                        ->body('Data diproses di background. Pantau di Import Monitor.')
                        ->success()
                        ->send();
                }),
                
            \Filament\Actions\CreateAction::make(),
        ];
    }
}