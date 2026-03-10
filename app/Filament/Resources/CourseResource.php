<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'LMS';
    protected static ?string $navigationLabel = 'Courses';
    protected static ?string $modelLabel = 'Course';
    protected static ?string $pluralModelLabel = 'Courses';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Course Tabs')
                    ->tabs([
                        // ==========================================
                        // TAB 1: DATA COURSE
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('Data Course')
                            ->icon('heroicon-o-book-open')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        
                                        // BAGIAN KIRI (Porsi 2 Kolom)
                                        Forms\Components\Section::make('Informasi Utama')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Course')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->readOnly(),

                                                Forms\Components\Select::make('category_id')
                                                    ->label('Kategori')
                                                    ->relationship('category', 'name')
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->createOptionForm([
                                                        Forms\Components\TextInput::make('name')
                                                            ->required()
                                                            ->live(onBlur: true)
                                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                                                        Forms\Components\TextInput::make('slug')->required(),
                                                        Forms\Components\Toggle::make('is_active')->default(true),
                                                    ]),

                                                Forms\Components\Textarea::make('description')
                                                    ->label('Deskripsi Singkat')
                                                    ->rows(4)
                                                    ->columnSpanFull(),
                                            ])->columns(2)->columnSpan(2),

                                        // BAGIAN KANAN (Porsi 1 Kolom)
                                        Forms\Components\Section::make('Media & Pengaturan')
                                            ->schema([
                                                Forms\Components\FileUpload::make('image')
                                                    ->label('Gambar Sampul')
                                                    ->image()
                                                    ->directory('courses')
                                                    ->required(),

                                                Forms\Components\DatePicker::make('start_date')
                                                    ->label('Tanggal Mulai')
                                                    ->native(false)
                                                    ->required(),
                                                
                                                // Kolom Total Modul (curriculum_count) SUDAH DIHAPUS DARI SINI

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Publikasikan')
                                                    ->default(true)
                                                    ->onColor('success'),
                                                
                                                Forms\Components\Toggle::make('has_certificate')
                                                    ->label('Dapat Sertifikat')
                                                    ->default(true)
                                                    ->onColor('success'),
                                            ])->columnSpan(1),
                                            
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: ASSIGN JABATAN WAJIB
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('Assign Jabatan Wajib')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Forms\Components\CheckboxList::make('positions')
                                    ->label('Pilih jabatan apa saja yang wajib mengikuti course ini.')
                                    ->relationship(
                                        name: 'positions', 
                                        titleAttribute: 'name'
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->group ? " ({$record->group})" : ""))
                                    ->searchable()
                                    ->bulkToggleable()
                                    ->columns(3)
                                    ->gridDirection('row'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Sampul')
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge(),

                // Di tabel tetap kita tampilkan hasil perhitungannya secara real-time
                Tables\Columns\TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label('Jml. Modul')
                    ->badge()
                    ->color('info'),

                Tables\Columns\ToggleColumn::make('is_active')->label('Aktif'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->relationship('category', 'name'),
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
            RelationManagers\ModulesRelationManager::class,
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
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),
        ];
    }
}