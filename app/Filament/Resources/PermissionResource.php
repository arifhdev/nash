<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermissionResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission; // Import Model Spatie

class PermissionResource extends Resource
{
    protected static ?string $model = Permission::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 98;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Permission')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->placeholder('Contoh: access_lms'),

                Forms\Components\TextInput::make('guard_name')
                    ->label('Guard')
                    ->default('web')
                    ->required()
                    ->maxLength(255)
                    ->disabled(), // Biasanya default 'web' dan tidak perlu diubah
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Permission')
                    ->searchable()
                    ->sortable()
                    ->copyable(), // Fitur copy biar mudah saat coding policy

                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ManagePermissions::route('/'),
        ];
    }

        // Logic: Hanya Super Admin yang boleh melihat menu ini
        public static function canViewAny(): bool
        {
            return auth()->check() && auth()->user()->hasRole('super_admin');
        }
}