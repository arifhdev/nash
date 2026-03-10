<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HondaIdStagingResource\Pages;
use App\Models\HondaIdStaging;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class HondaIdStagingResource extends Resource
{
    protected static ?string $model = HondaIdStaging::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Monitor Honda ID Temp';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 100;

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }
    public static function canDelete(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('honda_id')
                    ->label('Honda ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('md_code')
                    ->label('MD Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('dealer_code')
                    ->label('Dealer Code')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Import')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('5s')
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageHondaIdStagings::route('/'),
        ];
    }
}