<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Role')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Role')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            // Proteksi: Nama role sistem tidak boleh diganti
                            ->disabled(fn (?Role $record) => in_array($record?->name, ['super_admin', 'middle_admin', 'user'])),

                        Forms\Components\TextInput::make('guard_name')
                            ->label('Guard')
                            ->default('web')
                            ->readOnly() // Biasanya 'web', sebaiknya tidak diubah sembarangan
                            ->dehydrated(), // Tetap dikirim ke database
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'middle_admin' => 'warning',
                        'user' => 'success',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),

                // Menghitung berapa user yang punya role ini
                Tables\Columns\TextColumn::make('users_count')
                    ->counts('users')
                    ->label('Jumlah User')
                    ->badge()
                    ->color('info')
                    ->sortable(),

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
                
                // Proteksi: Cegah hapus role sistem
                Tables\Actions\DeleteAction::make()
                    ->before(function (Tables\Actions\DeleteAction $action, Role $record) {
                        if (in_array($record->name, ['super_admin', 'middle_admin', 'user'])) {
                            Notification::make()
                                ->danger()
                                ->title('Akses Ditolak')
                                ->body("Role '{$record->name}' adalah role sistem dan tidak boleh dihapus.")
                                ->send();
                            
                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                // Matikan bulk delete agar lebih aman
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    // Logic: Hanya Super Admin yang boleh melihat menu ini
    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }
}