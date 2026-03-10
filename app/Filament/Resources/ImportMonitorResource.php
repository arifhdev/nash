<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportMonitorResource\Pages;
use Filament\Actions\Imports\Models\Import;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

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
            ->poll('2s')
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
                    ->getStateUsing(function (Import $record) {
                        return (int) $record->processed_rows;
                    })
                    ->formatStateUsing(function ($state, Import $record) {
                        // Jika sudah ditandai selesai dari background job
                        if ($record->completed_at || $record->successful_rows > 0) {
                            return '<span class="text-sm font-bold text-green-600">Selesai 100%</span>';
                        }
                        
                        // Karena kita pakai Bulk Insert, angka akan diam di 0 sampai detik terakhir
                        if ($state === 0) {
                            return '
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-amber-500 animate-pulse">Memproses Bulk Insert...</span>
                                </div>
                            ';
                        }
                        
                        return '<span class="text-sm font-bold text-blue-600">'.number_format($state).' Diproses</span>';
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('processed_rows')
                    ->label('Total Baris')
                    ->getStateUsing(fn (Import $record) => number_format((int)$record->total_rows) . ' Data')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('successful_rows')
                    ->label('Sukses Eksekusi')
                    ->color('success')
                    ->numeric(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Mulai')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->actions([
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