<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PositionResource\Pages;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PositionResource extends Resource
{
    protected static ?string $model = Position::class;

    // Icon Koper (Cocok untuk Jabatan)
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    // Grouping di Sidebar
    protected static ?string $navigationGroup = 'Master Data';
    
    // Urutan menu (Opsional, angka kecil makin atas)
    protected static ?int $navigationSort = 12;

    // Label di Sidebar & Judul Halaman
    protected static ?string $navigationLabel = 'Jabatan';
    protected static ?string $modelLabel = 'Jabatan';
    protected static ?string $pluralModelLabel = 'Data Jabatan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Jabatan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Sales Counter, Kepala Bengkel')
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('group')
                                    ->label('Group / Departemen')
                                    ->placeholder('Contoh: Sales, Service, Finance')
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('level')
                                    ->label('Level')
                                    ->placeholder('Contoh: Staff, Supervisor, Manager')
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

                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->badge() 
                    // [PERBAIKAN] Tambah ?string agar tidak error jika nilainya null di database
                    ->color(fn (?string $state): string => match ($state) {
                        'Sales' => 'success',
                        'Service' => 'warning',
                        'Finance', 'Keuangan' => 'info',
                        'HRD', 'GA' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // [PERBAIKAN] Filter berdasarkan Group dengan mengabaikan nilai null/kosong
                Tables\Filters\SelectFilter::make('group')
                    ->options(fn() => Position::query()
                        ->whereNotNull('group')
                        ->where('group', '!=', '')
                        ->pluck('group', 'group')
                        ->toArray()
                    )
                    ->label('Filter Group'),
                
                // [PERBAIKAN] Filter berdasarkan Level dengan mengabaikan nilai null/kosong
                Tables\Filters\SelectFilter::make('level')
                    ->options(fn() => Position::query()
                        ->whereNotNull('level')
                        ->where('level', '!=', '')
                        ->pluck('level', 'level')
                        ->toArray()
                    )
                    ->label('Filter Level'),
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
        return [
            //
        ];
    }

    // Logic: Hanya Super Admin yang boleh melihat menu ini
    public static function canViewAny(): bool
    {
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