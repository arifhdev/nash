<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainerIdStagingResource\Pages;
use App\Models\TrainerIdStaging;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class TrainerIdStagingResource extends Resource
{
    protected static ?string $model = TrainerIdStaging::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Monitor Trainer ID Temp';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 103;

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainer_id')
                    ->label('Trainer ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama (Excel)')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('divisi')
                    ->label('Kode Divisi (Excel)')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan (Excel)')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Import')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('3s')
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTrainerIdStagings::route('/'),
        ];
    }
}