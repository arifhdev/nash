<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AhmIdStagingResource\Pages;
use App\Models\AhmIdStaging;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class AhmIdStagingResource extends Resource
{
    protected static ?string $model = AhmIdStaging::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Monitor AHM ID Temp';
    protected static ?string $navigationGroup = 'System';
    protected static ?int $navigationSort = 101;

    public static function canCreate(): bool { return false; }
    public static function canEdit(Model $record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ahm_id')
                    ->label('AHM ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                // Menampilkan data teks asli dari Excel
                Tables\Columns\TextColumn::make('divisi')
                    ->label('Divisi (Excel)')
                    ->badge()
                    ->color('info')
                    ->placeholder('Kosong'),

                Tables\Columns\TextColumn::make('jabatan')
                    ->label('Jabatan (Excel)')
                    ->badge()
                    ->color('gray')
                    ->placeholder('Kosong'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Import')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('3s')
            ->actions([
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageAhmIdStagings::route('/'),
        ];
    }
}