<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrainerIdVerificationResource\Pages;
use App\Models\TrainerIdVerification;
use App\Models\Division;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\TextInput::make('trainer_id')
                            ->label('Trainer ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->relationship('mainDealer', 'name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} - {$record->code}")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->required(),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Pekerjaan')
                    ->schema([
                        // --- UPDATE: MENGGUNAKAN MASTER DIVISI ---
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->options(Division::query()->pluck('name', 'id'))
                            ->live()
                            ->dehydrated(false)
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih Divisi Master')
                            ->afterStateUpdated(fn (Set $set) => $set('position_id', null))
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                if ($record && $record->position) {
                                    $set('division_id', $record->position->division_id);
                                }
                            }),

                        Forms\Components\Select::make('position_id')
                            ->label('Jabatan')
                            ->relationship('position', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                $divisionId = $get('division_id');
                                if ($divisionId) {
                                    // Trainer biasanya bernaung di user_type 'main_dealer'
                                    return $query->where('division_id', $divisionId)->where('user_type', 'main_dealer');
                                }
                                return $query->where('id', 0);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih Jabatan (Pilih Divisi Dulu)')
                            ->disabled(fn (Get $get) => empty($get('division_id'))),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Akun')
                    ->schema([
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
                Tables\Columns\TextColumn::make('trainer_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                // --- Tampilkan Nama Divisi dari Relasi ---
                Tables\Columns\TextColumn::make('position.division.name')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('mainDealer.name')
                    ->label('Main Dealer')
                    ->formatStateUsing(fn ($state, $record) => $state ? "{$state} - {$record->mainDealer->code}" : '-')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('has_account')
                    ->label('Daftar')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('division')
                    ->label('Filter Divisi')
                    ->relationship('position.division', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('main_dealer_id')
                    ->relationship('mainDealer', 'name')
                    ->label('Filter Main Dealer')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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