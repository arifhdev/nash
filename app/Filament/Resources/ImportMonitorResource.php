<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportMonitorResource\Pages;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ImportMonitorResource extends Resource
{
    protected static ?string $model = Import::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    protected static ?string $navigationLabel = 'Import Monitor';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 99;

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')
                    ->label('Nama File')
                    ->searchable()
                    ->description(fn (Import $record): string => class_basename($record->importer)),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diimport Oleh')
                    ->sortable(),

                // --- PROGRESS BAR VISUAL ---
                Tables\Columns\TextColumn::make('progress')
                    ->label('Status Sistem')
                    ->getStateUsing(fn (Import $record) => (int) $record->processed_rows)
                    ->formatStateUsing(function ($state, Import $record) {
                        // 1. STATUS SELESAI
                        if ($record->completed_at || ($record->successful_rows > 0 && $record->successful_rows >= $record->total_rows)) {
                            return '<div class="flex items-center gap-1 text-green-600 font-bold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg> Selesai 100%</div>';
                        }
                        
                        // 2. STATUS STUCK (Jika lebih dari 10 menit tidak ada update)
                        $isStuck = $record->updated_at->diffInMinutes(now()) > 10;
                        if ($isStuck && !$record->completed_at) {
                            return '<div class="flex items-center gap-1 text-red-500 font-bold"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg> Terhenti / Stuck</div>';
                        }

                        // 3. STATUS SEDANG JALAN
                        if ($state === 0) {
                            return '
                                <div class="flex items-center gap-2">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                    </span>
                                    <span class="text-sm font-medium text-amber-500">Memproses Bulk Insert...</span>
                                </div>
                            ';
                        }
                        
                        return '<span class="text-sm font-bold text-blue-600">'.number_format($state).' Baris Diproses</span>';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('successful_rows')
                    ->label('Data Sukses')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray')
                    ->numeric(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->actions([
                // ACTION BARU: Untuk memaksa status selesai jika log macet
                Tables\Actions\Action::make('forceComplete')
                    ->label('Set Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Paksa Selesai Log Import')
                    ->modalDescription('Gunakan ini hanya jika tampilan stuck padahal data sudah masuk di database. Ini akan menandai log sebagai "Selesai".')
                    ->hidden(fn (Import $record) => $record->completed_at)
                    ->action(function (Import $record) {
                        $record->update([
                            'completed_at' => now(),
                            'successful_rows' => $record->processed_rows ?: $record->total_rows,
                        ]);
                    }),

                Tables\Actions\DeleteAction::make()->label('Hapus Log'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageImportMonitors::route('/'),
        ];
    }
}