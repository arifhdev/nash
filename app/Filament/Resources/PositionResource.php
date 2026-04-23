<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get; 
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rules\Unique; 

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 14;

    protected static ?string $navigationLabel = 'Jabatan';
    protected static ?string $modelLabel = 'Jabatan';
    protected static ?string $pluralModelLabel = 'Data Jabatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jabatan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('user_type')
                                    ->label('Peruntukan Jabatan')
                                    ->options([
                                        'ahm' => 'Karyawan AHM',
                                        'main_dealer' => 'Karyawan Main Dealer',
                                        'dealer' => 'Karyawan Dealer',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->live(),

                                Forms\Components\TextInput::make('name')
                                    ->label('Nama Jabatan')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true) 
                                    ->placeholder('Contoh: Manager, Supervisor, Kepala Bengkel'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                // PERBAIKAN: Menggunakan Relasi ke Tabel Divisions
                                Forms\Components\Select::make('division_id')
                                    ->label('Divisi')
                                    ->relationship('division', 'name') // Mengambil data dari tabel divisions
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->native(false)
                                    // UPDATE LOGIKA: Cek keunikan berdasarkan Nama + Tipe User + division_id
                                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, Get $get) {
                                        return $rule->where('name', $get('name'))
                                                    ->where('user_type', $get('user_type'))
                                                    ->where('division_id', $get('division_id'));
                                    })
                                    ->validationMessages([
                                        'unique' => 'Jabatan ini sudah terdaftar untuk tipe user dan divisi tersebut.',
                                    ])
                                    ->placeholder('Pilih Divisi Master'),

                                Forms\Components\TextInput::make('level')
                                    ->label('Level')
                                    ->placeholder('Contoh: 1, 2, 3 atau Staff, Manager')
                                    ->maxLength(255),
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('user_type')
                    ->label('Peruntukan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ahm' => 'danger',
                        'main_dealer' => 'warning',
                        'dealer' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'ahm' => 'AHM',
                        'main_dealer' => 'Main Dealer',
                        'dealer' => 'Dealer',
                        default => $state,
                    })
                    ->sortable(),

                // PERBAIKAN: Menampilkan Nama Divisi dari Relasi
                Tables\Columns\TextColumn::make('division.name')
                    ->label('Divisi Master')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_type')
                    ->options([
                        'ahm' => 'AHM',
                        'main_dealer' => 'Main Dealer',
                        'dealer' => 'Dealer',
                    ])
                    ->label('Filter Peruntukan'),

                // PERBAIKAN: Filter Divisi sekarang dinamis dari Database
                Tables\Filters\SelectFilter::make('division_id')
                    ->label('Filter Divisi')
                    ->relationship('division', 'name')
                    ->searchable()
                    ->preload(),
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

    public static function canViewAny(): bool
    {
        // Tetap menggunakan gate super_admin seperti request kamu sebelumnya
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPositions::route('/'),
            'create' => Pages\CreatePosition::route('/create'),
            'edit' => Pages\EditPosition::route('/{record}/edit'),
        ];
    }
}