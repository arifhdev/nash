<?php

namespace App\Filament\Resources\ModuleResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';
    protected static ?string $title = 'Lessons';

    public function form(Forms\Form $form): Forms\Form
    {
        // Form ini hanya muncul jika kita klik "Create" (Buat baru) dari dalam modul
        // Tapi biasanya kita akan pakai "Attach" (Ambil yang sudah ada)
        return $form->schema([
            Forms\Components\TextInput::make('title')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul Pelajaran'),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextInputColumn::make('sort_order')->label('Urutan'), // Bisa edit urutan langsung
            ])
            ->filters([])
            ->headerActions([
                // AttachAction: Tombol untuk memilih pelajaran yang sudah ada
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        // Saat attach, kita bisa set urutannya
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ]),
            ])
            ->actions([
                // Detach: Lepaskan pelajaran dari modul (tidak menghapus pelajaran asli)
                Tables\Actions\DetachAction::make(),
            ])
            ->reorderable('sort_order'); // Fitur drag & drop urutan (Filament v3)
    }
}