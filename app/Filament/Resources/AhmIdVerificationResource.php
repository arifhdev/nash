<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AhmIdVerificationResource\Pages;
use App\Models\AhmIdVerification;
use App\Models\Position; // Jangan lupa import model Position
use App\Enums\UserType; // Opsional, jika kamu pakai Enum untuk filter query
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

                        // --- TAMBAHAN DIVISI & JABATAN (SINGLE) ---
                        Forms\Components\Select::make('divisi')
                            ->label('Divisi')
                            ->options(function () {
                                // Ambil daftar divisi khusus untuk karyawan AHM
                                return Position::query()
                                    ->where('user_type', 'ahm') // Sesuaikan jika value enum-mu berbeda (misal: UserType::AHM->value)
                                    ->whereNotNull('divisi')
                                    ->distinct()
                                    ->pluck('divisi', 'divisi');
                            })
                            ->live()
                            ->dehydrated(false) // Field virtual, tidak masuk ke db `ahm_id_verifications`
                            ->placeholder('Pilih Divisi')
                            ->afterStateUpdated(fn (Set $set) => $set('position_id', null)) // Reset jabatan jika divisi diganti
                            ->afterStateHydrated(function (Set $set, ?Model $record) {
                                // Auto-fill divisi saat mode Edit
                                if ($record && $record->position) {
                                    $set('divisi', $record->position->divisi);
                                }
                            }),

                        Forms\Components\Select::make('position_id')
                            ->label('Jabatan')
                            ->relationship('position', 'name', modifyQueryUsing: function (Builder $query, Get $get) {
                                $divisi = $get('divisi');

                                if ($divisi) {
                                    return $query->where('divisi', $divisi)->where('user_type', 'ahm');
                                }
                                return $query->where('id', 0); // Kosongkan pilihan jika divisi belum dipilih
                            })
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih Jabatan (Pilih Divisi Dulu)')
                            ->disabled(fn (Get $get) => empty($get('divisi'))),
                        // --- END TAMBAHAN ---
                        
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

                // --- TAMBAHAN KOLOM TABEL ---
                Tables\Columns\TextColumn::make('position.divisi')
                    ->label('Divisi')
                    ->badge()
                    ->color('info')
                    ->placeholder('N/A')
                    ->searchable(),

                Tables\Columns\TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->badge()
                    ->color('gray')
                    ->placeholder('N/A')
                    ->searchable(),
                // --- END TAMBAHAN ---

                Tables\Columns\IconColumn::make('is_active')->label('Aktif?')->boolean(),
                Tables\Columns\IconColumn::make('has_account')->label('Sudah Daftar?')->boolean(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Input')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Opsional: Filter berdasarkan Divisi / Jabatan bisa ditambahkan di sini
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