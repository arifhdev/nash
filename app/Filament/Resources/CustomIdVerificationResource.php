<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomIdVerificationResource\Pages;
use App\Models\CustomIdVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CustomIdVerificationResource extends Resource
{
    protected static ?string $model = CustomIdVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'MD ID Whitelist';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi MD ID')
                    ->description('Masukkan data MD ID yang bernaung di bawah Main Dealer.')
                    ->schema([
                        Forms\Components\TextInput::make('custom_id')
                            ->label('MD ID')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                            
                        // Tambahan Input Nama
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->relationship('mainDealer', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->code}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->required(),
                            
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
                Tables\Columns\TextColumn::make('custom_id')
                    ->label('MD ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('MD ID berhasil disalin'),

                // Tambahan Kolom Nama di Tabel
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mainDealer.name')
                    ->label('Main Dealer')
                    ->formatStateUsing(fn ($state, $record) => $state ? "{$state} - {$record->mainDealer->code}" : '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif?')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('has_account')
                    ->label('Sudah Daftar?')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
                Tables\Filters\TernaryFilter::make('has_account')->label('Status Pendaftaran'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCustomIdVerifications::route('/'),
            'create' => Pages\CreateCustomIdVerification::route('/create'),
            'edit' => Pages\EditCustomIdVerification::route('/{record}/edit'),
        ];
    }
}