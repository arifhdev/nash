<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointHistoryResource\Pages;
use App\Models\PointHistory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model; // Tambahan untuk type-hinting

class PointHistoryResource extends Resource
{
    protected static ?string $model = PointHistory::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line'; // Diganti ke ikon chart/progress
    protected static ?string $navigationGroup = 'Gamification';
    
    // Karena sekarang menyimpan Poin dan XP, kita ubah labelnya
    protected static ?string $navigationLabel = 'Riwayat Reward';
    protected static ?string $pluralModelLabel = 'Riwayat Reward';
    protected static ?string $modelLabel = 'Riwayat Reward';
    protected static ?int $navigationSort = 1;

    // =========================================================================
    // Hapus hak akses Create, Edit, dan Delete agar tabel ini menjadi 
    // "Ledger" (Buku Besar) yang kebal manipulasi
    // =========================================================================
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.name')
                    ->label('Nama User'),
                Forms\Components\TextInput::make('amount')
                    ->label('Perubahan Poin (Currency)'),
                Forms\Components\TextInput::make('xp_amount')
                    ->label('Perubahan XP (Leaderboard)'), // Tambahan XP di Form
                Forms\Components\TextInput::make('description')
                    ->label('Keterangan Aktivitas'),
                Forms\Components\TextInput::make('created_at')
                    ->label('Tanggal & Waktu Transaksi')
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('user.mainDealer.name')
                    ->label('Main Dealer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan Aktivitas')
                    ->searchable(),

                // Kolom Poin (Mata Uang)
                Tables\Columns\TextColumn::make('amount')
                    ->label('Poin (Mata Uang)')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 0 => 'success',
                        $state < 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : $state)
                    ->sortable(),

                // Kolom XP (Tambahan Baru)
                Tables\Columns\TextColumn::make('xp_amount')
                    ->label('XP (Progress)')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 0 => 'warning',
                        $state < 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : $state)
                    ->sortable(),
            ])
            ->filters([
                // Filter berdasarkan Main Dealer
                Tables\Filters\SelectFilter::make('main_dealer')
                    ->label('Filter Main Dealer')
                    ->relationship('user.mainDealer', 'name'),
                    
                // Filter berdasarkan User Tertentu
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Filter User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Dikosongkan agar tidak ada yang bisa hapus massal
            ])
            ->defaultSort('created_at', 'desc'); // Urutkan dari yang paling baru
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            // Karena ini read-only, kita hanya butuh halaman index (List)
            'index' => Pages\ListPointHistories::route('/'),
        ];
    }
}