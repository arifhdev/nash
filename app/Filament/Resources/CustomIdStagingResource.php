<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomIdStagingResource\Pages;
use App\Models\CustomIdStaging;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CustomIdStagingResource extends Resource
{
    protected static ?string $model = CustomIdStaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Monitor MD ID Temp';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 102;

    // Kunci tabel agar Read-Only (Hanya untuk monitoring)
    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('custom_id')
                    ->label('MD ID') // Diubah agar seragam dengan Whitelist
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                    
                // Tambahan Kolom Nama
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Import')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s') // Auto refresh setiap 5 detik
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCustomIdStagings::route('/'),
        ];
    }
}