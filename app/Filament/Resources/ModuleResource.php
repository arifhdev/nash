<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Pages;
use App\Filament\Resources\ModuleResource\RelationManagers;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    // Mengelompokkan menu di sidebar agar rapi
    protected static ?string $navigationGroup = 'LMS';
    
    protected static ?string $navigationLabel = 'Modules';
    protected static ?string $modelLabel = 'Module';
    protected static ?string $pluralModelLabel = 'Modules';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Module')
                    ->description('Buat wadah modul, lalu isi pelajaran di halaman Edit.')
                    ->schema([
                        // Nama Modul
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Module')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true) // Generate slug otomatis
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                        // Slug (URL)
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->readOnly(),

                        // Deskripsi
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),

                        // Status Aktif
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->onColor('success'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Module')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // FITUR BARU: Menampilkan Course Terkait
                Tables\Columns\TextColumn::make('courses.title')
                    ->label('Course Terkait')
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->limitList(3) // Batasi tampilan max 3 course agar baris tidak terlalu tinggi
                    ->expandableLimitedList(), // Sisanya bisa diklik untuk di-expand (dilihat semua)

                // Menampilkan jumlah pelajaran yang ada di dalam modul ini
                Tables\Columns\TextColumn::make('lessons_count')
                    ->label('Jml. Pelajaran')
                    ->counts('lessons')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
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

    public static function getRelations(): array
    {
        return [
            // Daftarkan Relation Manager agar bisa attach Lesson di dalam Module
            RelationManagers\LessonsRelationManager::class,
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
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}