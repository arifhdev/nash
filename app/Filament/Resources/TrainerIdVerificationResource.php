<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainerIdVerificationResource\Pages;
use App\Models\TrainerIdVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TrainerIdVerificationResource extends Resource
{
    protected static ?string $model = TrainerIdVerification::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap'; 
    protected static ?string $navigationLabel = 'Trainer ID Whitelist';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Trainer ID')
                    ->description('Masukkan data Trainer ID yang bernaung di bawah Main Dealer.')
                    ->schema([
                        Forms\Components\TextInput::make('trainer_id')
                            ->label('Trainer ID')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        // Field Nama Lengkap yang ditarik dari DB
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
                    ])->columns(3), // Diubah ke 3 kolom agar satu baris pas: ID, Nama, MD

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika dimatikan, Trainer ID ini tidak bisa digunakan untuk mendaftar.')
                            ->default(true),

                        Forms\Components\Toggle::make('has_account')
                            ->label('Sudah Punya Akun?')
                            ->helperText('Otomatis dicentang oleh sistem jika user sudah berhasil mendaftar.')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('trainer_id')
                    ->label('Trainer ID')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                // Kolom Nama Lengkap di Tabel
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
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_account')
                    ->label('Sudah Daftar?')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('has_account')->label('Status Pendaftaran'),
                Tables\Filters\TernaryFilter::make('is_active')->label('Status Aktif'),
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
            'index' => Pages\ListTrainerIdVerifications::route('/'),
            'create' => Pages\CreateTrainerIdVerification::route('/create'),
            'edit' => Pages\EditTrainerIdVerification::route('/{record}/edit'),
        ];
    }
}