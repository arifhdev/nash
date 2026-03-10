<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonResource\Pages;
use App\Models\Lesson;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'LMS';
    protected static ?string $navigationLabel = 'Lessons';
    protected static ?string $modelLabel = 'Lesson';
    protected static ?string $pluralModelLabel = 'Lessons';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Pelajaran')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Pelajaran')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->readOnly(),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Pelajaran')
                            ->options([
                                'video' => 'Video',
                                'text'  => 'Artikel',
                                'quiz'  => 'Kuis',
                            ])
                            ->default('video')
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset durasi jika tipe berubah
                                $set('duration_minutes', 0);
                            }),

                        // --- LOGIC VIDEO ---
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL Video (YouTube/Vimeo)')
                            ->visible(fn (Get $get) => $get('type') === 'video')
                            ->required(fn (Get $get) => $get('type') === 'video')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->columnSpanFull(),
                        
                        // --- LOGIC TEXT (ARTIKEL) ---
                        // Menggunakan live(debounce) untuk hitung kata otomatis
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Artikel')
                            ->visible(fn (Get $get) => $get('type') === 'text')
                            ->required(fn (Get $get) => $get('type') === 'text')
                            ->live(debounce: 1000) // Tunggu 1 detik setelah ketik baru hitung
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                // Hitung Estimasi Waktu Baca (Reading Time)
                                // Rumus: Jumlah Kata / 200 kata per menit
                                if ($state) {
                                    $cleanText = strip_tags($state); // Hapus tag HTML
                                    $wordCount = str_word_count($cleanText);
                                    $minutes   = ceil($wordCount / 200); // Pembulatan ke atas
                                    $set('duration_minutes', $minutes < 1 ? 1 : $minutes);
                                }
                            })
                            ->columnSpanFull(),

                        // --- LOGIC QUIZ ---
                        Forms\Components\Repeater::make('quiz_data')
                            ->label('Pertanyaan Kuis')
                            ->visible(fn (Get $get) => $get('type') === 'quiz')
                            ->schema([
                                Forms\Components\Textarea::make('question')
                                    ->label('Pertanyaan')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('option_a')->label('Opsi A')->required(),
                                    Forms\Components\TextInput::make('option_b')->label('Opsi B')->required(),
                                    Forms\Components\TextInput::make('option_c')->label('Opsi C'), // Opsional
                                    Forms\Components\TextInput::make('option_d')->label('Opsi D'), // Opsional
                                ]),
                                Forms\Components\Select::make('correct_answer')
                                    ->label('Jawaban Benar')
                                    ->options([
                                        'a' => 'A', 
                                        'b' => 'B',
                                        'c' => 'C',
                                        'd' => 'D',
                                    ])->required(),
                            ])
                            ->columnSpanFull()
                            ->collapsible(),

                        // --- DURASI (AUTO UNTUK TEXT, MANUAL UNTUK VIDEO) ---
                        Forms\Components\TextInput::make('duration_minutes')
                            ->label('Durasi (Menit)')
                            ->numeric()
                            ->default(0)
                            ->helperText(fn (Get $get) => $get('type') === 'text' 
                                ? 'Otomatis dihitung berdasarkan jumlah kata.' 
                                : 'Masukkan durasi video secara manual.')
                            // Readonly hanya jika text, video boleh edit manual
                            ->readOnly(fn (Get $get) => $get('type') === 'text') 
                            ->visible(fn (Get $get) => in_array($get('type'), ['video', 'text'])),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktifkan Pelajaran')
                            ->default(true)
                            ->onColor('success'),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('modules.courses'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Pelajaran')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'video' => 'info',
                        'text' => 'success',
                        'quiz' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('modules.name')
                    ->label('Module Terkait')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->limitList(3)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('courses')
                    ->label('Course Terkait')
                    ->badge()
                    ->color('success')
                    ->getStateUsing(function ($record) {
                        return $record->modules->flatMap->courses->pluck('title')->unique()->toArray();
                    })
                    ->limitList(3)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('duration_minutes')
                    ->label('Durasi')
                    ->suffix(' menit')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->filters([
                // 1. Filter Tipe
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'video' => 'Video',
                        'text'  => 'Artikel',
                        'quiz'  => 'Kuis',
                    ])
                    ->label('Tipe Pelajaran'),

                // 2. Filter Module Terkait (Relasi Langsung)
                Tables\Filters\SelectFilter::make('modules')
                    ->relationship('modules', 'name')
                    ->label('Module Terkait')
                    ->searchable()
                    ->preload()
                    ->multiple(), // Bisa pilih lebih dari 1 modul

                // 3. Filter Course Terkait (Custom Query Bertingkat)
                Tables\Filters\SelectFilter::make('course')
                    ->label('Course Terkait')
                    ->options(fn() => \App\Models\Course::pluck('title', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            // Filter lesson yang punya relasi ke modul, yang mana modulnya punya relasi ke course yg dipilih
                            $query->whereHas('modules.courses', function (Builder $query) use ($data) {
                                $query->where('courses.id', $data['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->preload(),
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
            //
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            'create' => Pages\CreateLesson::route('/create'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
    }
}