<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GamificationSettingResource\Pages;
use App\Models\GamificationSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GamificationSettingResource extends Resource
{
    protected static ?string $model = GamificationSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Gamification';
    protected static ?string $navigationLabel = 'Setting Reward';
    protected static ?string $pluralModelLabel = 'Setting Reward';

    // Pastikan admin hanya bisa membuat 1 baris pengaturan saja (Singleton)
    public static function canCreate(): bool
    {
        return GamificationSetting::count() === 0;
    }

    public static function canDelete(int|\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Tidak boleh dihapus
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Reward Daily Check-in')
                    ->description('Tentukan berapa Poin dan XP yang didapat siswa saat check-in harian.')
                    ->schema([
                        Forms\Components\TextInput::make('daily_checkin_points')
                            ->label('Poin Harian (Mata Uang)')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-banknotes')
                            ->helperText('Bisa digunakan untuk ditukar (Redeem) nantinya.'),

                        Forms\Components\TextInput::make('daily_checkin_xp')
                            ->label('XP Harian (Leaderboard)')
                            ->numeric()
                            ->required()
                            ->prefixIcon('heroicon-o-bolt')
                            ->helperText('Digunakan untuk menaikkan peringkat (Life-time, tidak bisa dikurangi).'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('daily_checkin_points')
                    ->label('Reward Poin')
                    ->badge()
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('daily_checkin_xp')
                    ->label('Reward XP')
                    ->badge()
                    ->color('warning'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Ubah Nominal Reward'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageGamificationSettings::route('/'),
        ];
    }
}