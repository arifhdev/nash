<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserActivityResource\Pages;
use App\Models\UserActivity;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UserActivityResource extends Resource
{
    protected static ?string $model = UserActivity::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    // Masukkan ke grup Laporan
    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Aktivitas User';
    
    protected static ?string $pluralModelLabel = 'Riwayat Aktivitas User';

    protected static ?int $navigationSort = 2;

    // MATIKAN CREATE BIAR JADI READ-ONLY REPORT
    public static function canCreate(): bool
    {
        return false;
    }

    // MATIKAN EDIT
    public static function canEdit(Model|\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Kosongkan karena tidak ada aksi Edit/Create
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc') // Urutkan dari yang paling baru
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (UserActivity $record) => $record->user->email ?? '-'),

                Tables\Columns\TextColumn::make('activity_type')
                    ->label('Tipe Aktivitas')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Login' => 'success',
                        'Logout' => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Aktivitas')
                    ->dateTime('d M Y, H:i:s')
                    ->timezone('Asia/Jakarta') // Paksa ke format WIB
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),

                Tables\Columns\TextColumn::make('user_agent')
                    ->label('Perangkat / Browser')
                    ->limit(30)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 30 ? $state : null;
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Sembunyikan default karena teksnya panjang
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('activity_type')
                    ->label('Filter Aktivitas')
                    ->options([
                        'Login' => 'Login',
                        'Logout' => 'Logout',
                    ]),
                
                // Filter Tanggal
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                // Opsional: Matikan jika tidak mau ada tombol delete per-baris
                // Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ManageUserActivities::route('/'),
        ];
    }
}