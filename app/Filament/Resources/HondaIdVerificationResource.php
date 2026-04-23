<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HondaIdVerificationResource\Pages;
use App\Models\HondaIdVerification;
use App\Models\Division;
use App\Models\Position;
use App\Models\MainDealer;
use App\Models\Dealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
                Forms\Components\Section::make('Informasi Utama')
                    ->schema([
                        Forms\Components\TextInput::make('honda_id')
                            ->label('Honda ID')
                            ->required()
                            ->unique(ignoreRecord: true),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap'),
                            
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

                Forms\Components\Section::make('Detail Pekerjaan')
                    ->schema([
                        Forms\Components\Select::make('division_id')
                            ->label('Divisi')
                            ->options(Division::query()->pluck('name', 'id'))
                            ->live()
                            ->dehydrated(false)
                            ->searchable()
                            ->preload()
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
                                    return $query->where('division_id', $divisionId)->where('user_type', 'dealer');
                                }
                                return $query->where('id', 0);
                            })
                            ->searchable()
                            ->preload()
                            ->required()
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
                Tables\Columns\TextColumn::make('honda_id')
                    ->label('ID')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position.division.name')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray')
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
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_account')
                    ->label('Daftar')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                // FILTER STATUS AKTIF
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Tidak Aktif'),

                Tables\Filters\SelectFilter::make('division')
                    ->label('Filter Divisi')
                    ->relationship('position.division', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\TernaryFilter::make('has_account')->label('Status Pendaftaran'),

                // Filter Main Dealer (agar sinkron dengan URL dari Widget)
                Tables\Filters\SelectFilter::make('main_dealer_id')
                    ->label('Filter Main Dealer')
                    ->options(MainDealer::pluck('name', 'id'))
                    ->searchable(),

                // Filter Dealer (agar sinkron dengan URL dari Widget)
                Tables\Filters\SelectFilter::make('dealer_id')
                    ->label('Filter Dealer')
                    ->options(Dealer::pluck('name', 'id'))
                    ->searchable(),

                // Filter Position/Jabatan (agar sinkron dengan URL dari Widget)
                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Filter Jabatan')
                    ->options(Position::pluck('name', 'id'))
                    ->searchable(),
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
            'index' => Pages\ListHondaIdVerifications::route('/'),
            'create' => Pages\CreateHondaIdVerification::route('/create'),
            'edit' => Pages\EditHondaIdVerification::route('/{record}/edit'),
        ];
    }
}