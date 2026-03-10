<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AhmIdVerificationResource\Pages;
use App\Models\AhmIdVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AhmIdVerificationResource extends Resource
{
    protected static ?string $model = AhmIdVerification::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'AHM ID Whitelist';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi AHM ID')
                    ->schema([
                        Forms\Components\TextInput::make('ahm_id')
                            ->label('AHM ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required() // Sesuaikan jika boleh kosong (nullable)
                            ->maxLength(255),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),

                        Forms\Components\Toggle::make('has_account')
                            ->label('Sudah Punya Akun?')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ahm_id')
                    ->label('AHM ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')->label('Aktif?')->boolean(),
                Tables\Columns\IconColumn::make('has_account')->label('Sudah Daftar?')->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAhmIdVerifications::route('/'),
            'create' => Pages\CreateAhmIdVerification::route('/create'),
            'edit' => Pages\EditAhmIdVerification::route('/{record}/edit'),
        ];
    }
}