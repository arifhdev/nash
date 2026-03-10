<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HondaIdVerificationResource\Pages;
use App\Models\HondaIdVerification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class HondaIdVerificationResource extends Resource
{
    protected static ?string $model = HondaIdVerification::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Honda ID Whitelist';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Honda ID')
                    ->description('Masukkan data Honda ID yang diizinkan untuk mendaftar.')
                    ->schema([
                        Forms\Components\TextInput::make('honda_id')
                            ->label('Honda ID')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                            
                        // CUMAN NAMBAHIN INI
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('position_id')
                            ->label('Jabatan')
                            ->relationship('position', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->group})")
                            ->searchable(['name', 'group'])
                            ->preload(),
                            
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->relationship('mainDealer', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->code}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('dealer_id', null)),

                        Forms\Components\Select::make('dealer_id')
                            ->label('Dealer')
                            ->relationship(
                                name: 'dealer',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('main_dealer_id', $get('main_dealer_id'))
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->code}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->disabled(fn (Get $get): bool => ! filled($get('main_dealer_id'))),
                    ])->columns(2),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika dimatikan, Honda ID ini tidak bisa digunakan untuk mendaftar.')
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
                Tables\Columns\TextColumn::make('honda_id')
                    ->label('Honda ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Honda ID berhasil disalin')
                    ->copyMessageDuration(1500),
                    
                // CUMAN NAMBAHIN INI
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->formatStateUsing(fn ($state, $record) => $state ? "{$state} ({$record->position->group})" : '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mainDealer.name')
                    ->label('Main Dealer')
                    ->formatStateUsing(fn ($state, $record) => $state ? "{$state} - {$record->mainDealer->code}" : '-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('dealer.name')
                    ->label('Dealer')
                    ->formatStateUsing(fn ($state, $record) => $state ? "{$state} - {$record->dealer->code}" : '-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

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
                Tables\Filters\TernaryFilter::make('has_account')
                    ->label('Status Pendaftaran')
                    ->placeholder('Semua')
                    ->trueLabel('Sudah Daftar')
                    ->falseLabel('Belum Daftar'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif')
                    ->falseLabel('Tidak Aktif'),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHondaIdVerifications::route('/'),
            'create' => Pages\CreateHondaIdVerification::route('/create'),
            'edit' => Pages\EditHondaIdVerification::route('/{record}/edit'),
        ];
    }
}