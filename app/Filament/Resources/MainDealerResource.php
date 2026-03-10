<?php

namespace App\Filament\Resources;

use App\Enums\MainDealerGroup; 
use App\Filament\Resources\MainDealerResource\Pages;
use App\Models\MainDealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MainDealerResource extends Resource
{
    protected static ?string $model = MainDealer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Main Dealer')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Main Dealer')
                            ->required()
                            ->maxLength(255),
                            
                        // Kode MD hanya boleh diedit oleh Super Admin agar tidak merusak relasi -HO
                        Forms\Components\TextInput::make('code')
                            ->label('Kode MD')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->disabled(fn () => !auth()->user()->hasRole('super_admin')),

                        Forms\Components\Select::make('group')
                            ->label('Grup Perusahaan')
                            ->options(MainDealerGroup::class)
                            ->searchable()
                            ->placeholder('Pilih Grup (Opsional)')
                            ->nullable()
                            ->disabled(fn () => !auth()->user()->hasRole('super_admin')),
                            
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                Tables\Columns\TextColumn::make('group')
                    ->label('Grup Perusahaan')
                    ->badge()
                    ->placeholder('-')
                    ->sortable(),

                Tables\Columns\TextColumn::make('dealers_count')
                    ->counts('dealers')
                    ->label('Jumlah Dealer')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->label('Filter Grup')
                    ->options(MainDealerGroup::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ])->visible(fn () => auth()->user()->hasRole('super_admin')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMainDealers::route('/'),
            'create' => Pages\CreateMainDealer::route('/create'),
            'edit' => Pages\EditMainDealer::route('/{record}/edit'),
        ];
    }
    
    /**
     * PERBAIKAN LOGIC:
     * Head Office (HO) bisa melihat semua Main Dealer dalam satu grup.
     * Cabang biasa tetap hanya bisa melihat dirinya sendiri.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Super Admin: Full Access
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // 2. Middle Admin (HO & Cabang)
        if ($user->hasRole('middle_admin')) {
            $md = $user->mainDealer;

            if ($md && $md->isHeadOffice()) {
                // HO bisa lihat semua MD di grup yang sama (misal: semua astra_motor)
                return $query->where('group', $md->group);
            }

            // Cabang biasa cuma lihat diri sendiri
            return $query->where('id', $user->main_dealer_id);
        }

        return $query->whereRaw('1 = 0');
    }
}