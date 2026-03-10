<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DealerResource\Pages;
use App\Models\Dealer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DealerResource extends Resource
{
    protected static ?string $model = Dealer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    
    protected static ?string $navigationGroup = 'Master Data';
    
    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Dealer')
                    ->schema([
                        // LOGIC: Pemilihan Main Dealer yang Dinamis berdasarkan hierarki
                        Forms\Components\Select::make('main_dealer_id')
                            ->label('Main Dealer')
                            ->relationship('mainDealer', 'name', modifyQueryUsing: function (Builder $query) {
                                $user = auth()->user();
                                if ($user->hasRole('super_admin')) return $query;
                                
                                $md = $user->mainDealer;
                                // Jika Head Office, bisa pilih MD lain di grup yang sama
                                if ($md?->isHeadOffice()) {
                                    return $query->where('group', $md->group);
                                }
                                // Cabang biasa terkunci di ID sendiri
                                return $query->where('id', $user->main_dealer_id);
                            })
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->user()->main_dealer_id)
                            // Disabled hanya jika dia Cabang Biasa (HO & Super Admin bisa edit)
                            ->disabled(fn () => !auth()->user()->hasRole('super_admin') && !auth()->user()->mainDealer?->isHeadOffice())
                            ->dehydrated(),
                            
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Dealer / AHASS')
                            ->required()
                            ->maxLength(255),
                            
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Dealer')
                            ->maxLength(255),
                            
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
                Tables\Columns\TextColumn::make('mainDealer.name')
                    ->label('Main Dealer')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info')
                    // Muncul untuk Super Admin ATAU Head Office
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->mainDealer?->isHeadOffice()),

                // --- TAMBAHAN: KOLOM GROUP PERUSAHAAN ---
                Tables\Columns\TextColumn::make('mainDealer.group')
                    ->label('Grup Perusahaan')
                    ->badge()
                    ->sortable()
                    // Penyesuaian warna berdasarkan group di DB Mas
                    ->colors([
                        'danger' => 'astra_motor',
                        'success' => 'cdn',
                        'warning' => 'mpm',
                        'info' => 'tunas_dwipa',
                    ])
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->mainDealer?->isHeadOffice()),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Dealer')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter Main Dealer yang menyesuaikan hierarki grup
                Tables\Filters\SelectFilter::make('main_dealer_id')
                    ->relationship('mainDealer', 'name', modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
                        if ($user->hasRole('super_admin')) return $query;
                        
                        $md = $user->mainDealer;
                        if ($md?->isHeadOffice()) {
                            return $query->where('group', $md->group);
                        }
                        return $query;
                    })
                    ->label('Filter Main Dealer')
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin') || auth()->user()->mainDealer?->isHeadOffice()),
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
            'index' => Pages\ListDealers::route('/'),
            'create' => Pages\CreateDealer::route('/create'),
            'edit' => Pages\EditDealer::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // 1. Super Admin: Penguasa tertinggi
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // 2. Middle Admin (HO & Cabang Biasa)
        if ($user->hasRole('middle_admin')) {
            $md = $user->mainDealer;

            // Jika dia Head Office
            if ($md?->isHeadOffice()) {
                // Pastikan group bersih dari spasi dan di-handle jika berupa Enum
                $targetGroup = trim($md->group instanceof \UnitEnum ? $md->group->value : $md->group);

                return $query->whereHas('mainDealer', function ($q) use ($targetGroup) {
                    $q->whereNotNull('group')
                      ->where('group', $targetGroup);
                });
            }

            // 3. Cabang Biasa: Hanya lihat dealer miliknya sendiri
            return $query->where('main_dealer_id', $user->main_dealer_id);
        }
        
        return $query->whereRaw('1 = 0');
    }
}