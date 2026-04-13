<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PointHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'pointHistories';
    protected static ?string $title = 'Riwayat Poin (Gamification)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Poin')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('description')
                    ->label('Keterangan')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal & Waktu')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan Aktivitas')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Poin')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state > 0 => 'success',
                        $state < 0 => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state > 0 ? "+{$state}" : $state),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tombol jika admin ingin memberikan poin manual (opsional)
                Tables\Actions\CreateAction::make()
                    ->label('Beri Poin Manual')
                    ->after(function ($livewire) {
                        // Update total_points di tabel users setelah poin manual ditambahkan
                        $user = $livewire->getOwnerRecord();
                        $user->update([
                            'total_points' => $user->pointHistories()->sum('amount')
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->after(function ($livewire) {
                        // Recalculate poin jika admin menghapus history
                        $user = $livewire->getOwnerRecord();
                        $user->update([
                            'total_points' => $user->pointHistories()->sum('amount')
                        ]);
                    }),
            ])
            ->bulkActions([
                // Kosongkan agar aman
            ])
            ->defaultSort('created_at', 'desc'); // Urutkan dari yang terbaru
    }
}