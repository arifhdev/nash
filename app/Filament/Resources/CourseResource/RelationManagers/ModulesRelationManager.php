<?php

namespace App\Filament\Resources\CourseResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'modules';
    protected static ?string $title = 'Modules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Modul')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Forms\Set $set, ?string $state) => $set('slug', Str::slug($state))),
                
                // PERBAIKAN: Tambahkan unique validation dan hapus readOnly() agar user bisa mengedit manual jika duplicate
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                    
                Forms\Components\Toggle::make('is_active')->default(true),

                // FITUR BARU: Kelola (Attach/Detach) Lesson langsung dari pop-up Edit Modul
                Forms\Components\Select::make('lessons')
                    ->label('Daftar Materi (Lessons)')
                    ->relationship('lessons', 'title') // Ubah 'title' menjadi 'name' jika tabel lesson menggunakan kolom 'name'
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull()
                    ->hint('Pilih materi untuk memasukkan, atau klik silang (x) untuk melepas materi dari modul ini.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Modul')
                    ->weight('bold'),
                
                // FITUR BARU: Menampilkan daftar lesson di tabel sebagai badge
                Tables\Columns\TextColumn::make('lessons.title') // Ubah 'title' menjadi 'name' jika perlu
                    ->label('Materi Terhubung')
                    ->badge()
                    ->color('success')
                    ->limitList(3) // Dibatasi 3 saja agar tabel tidak terlalu tinggi
                    ->expandableLimitedList(), // Bisa diklik untuk melihat sisa materi jika lebih dari 3

                Tables\Columns\TextColumn::make('lessons_count')
                    ->counts('lessons')
                    ->label('Total Materi')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextInputColumn::make('sort_order')
                    ->label('Urutan'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Ke')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ]),

                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Modul')
                    ->modalHeading('Edit Modul & Kelola Materi'), // Judul pop-up diubah biar lebih jelas
                    
                Tables\Actions\DetachAction::make()
                    ->label('Lepas Modul'), // Melepas modul dari course
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
    }
}