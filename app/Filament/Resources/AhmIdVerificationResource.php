<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AhmIdVerificationResource\Pages;
use App\Models\AhmIdVerification;
use App\Models\Division; // Import Model Division
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
                            ->required() 
                            ->maxLength(255),

                        // --- UPDATE: AMBIL DARI MASTER DIVISION ---
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->options(Division::query()->pluck('name', 'id')) // Ambil dari Tabel Division
                            ->live()
                            ->dehydrated(false)
                            ->searchable()
                            ->preload()
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
                                    return $query->where('division_id', $divisionId)->where('user_type', 'ahm');
                                }
                                return $query->where('id', 0);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Pilih Jabatan (Pilih Divisi Dulu)')
                            ->disabled(fn (Get $get) => empty($get('division_id'))),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
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

                // --- UPDATE: TAMPILKAN NAMA DIVISI DARI RELASI ---
                Tables\Columns\TextColumn::make('position.division.name')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->placeholder('N/A')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')->label('Aktif?')->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('division')
                    ->label('Filter Divisi')
                    ->relationship('position.division', 'name')
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
            'index' => Pages\ListAhmIdVerifications::route('/'),
            'create' => Pages\CreateAhmIdVerification::route('/create'),
            'edit' => Pages\EditAhmIdVerification::route('/{record}/edit'),
        ];
    }
}