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
                            ->unique(ignoreRecord: true), 

                        // TAUTKAN KE MODULE
                        Forms\Components\Select::make('modules')
                            ->relationship('modules', 'name')
                            ->label('Tautkan ke Module (Opsional)')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label('Tipe Pelajaran')
                            ->options([
                                'video' => 'Video',
                                'pdf'   => 'PDF Document',
                                'quiz'  => 'Kuis',
                            ])
                            ->default('video')
                            ->live()
                            ->required()
                            ->afterStateUpdated(function (Set $set) {
                                // Reset durasi jika tipe berubah
                                $set('duration_minutes', 0);
                            }),

                        // LOGIC VIDEO
                        Forms\Components\TextInput::make('video_url')
                            ->label('URL Video (YouTube/Vimeo)')
                            ->visible(fn (Get $get) => $get('type') === 'video')
                            ->required(fn (Get $get) => $get('type') === 'video')
                            ->placeholder('https://www.youtube.com/watch?v=...')
                            ->columnSpanFull(),
                        
                        // LOGIC PDF UPLOAD
                        Forms\Components\FileUpload::make('content')
                            ->label('Upload File PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('lessons/pdf') 
                            ->preserveFilenames()
                            ->openable()
                            ->downloadable()
                            ->visible(fn (Get $get) => $get('type') === 'pdf')
                            ->required(fn (Get $get) => $get('type') === 'pdf')
                            ->columnSpanFull(),

                        // LOGIC QUIZ SETTINGS
                        Forms\Components\TextInput::make('quiz_display_count')
                            ->label('Jumlah Pertanyaan Ditampilkan')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (Get $get) => $get('type') === 'quiz')
                            ->required(fn (Get $get) => $get('type') === 'quiz')
                            ->helperText('Misal: Anda buat 5 soal, tapi isi 3 disini. Maka user hanya akan mendapat 3 soal acak.')
                            ->rules([
                                fn (Get $get) => function (string $attribute, $value, \Closure $fail) use ($get) {
                                    $totalQuiz = count($get('quiz_data') ?? []);
                                    if ($totalQuiz > 0 && $value > $totalQuiz) {
                                        $fail("Jumlah yang ditampilkan ({$value}) tidak boleh melebihi total pertanyaan yang dibuat ({$totalQuiz}).");
                                    }
                                },
                            ]),

                        // LOGIC QUIZ
                        Forms\Components\Repeater::make('quiz_data')
                            ->label('Pertanyaan Kuis')
                            ->visible(fn (Get $get) => $get('type') === 'quiz')
                            ->live() 
                            ->schema([
                                Forms\Components\Textarea::make('question')
                                    ->label('Pertanyaan')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\Grid::make(2)->schema([
                                    Forms\Components\TextInput::make('option_a')->label('Opsi A')->required(),
                                    Forms\Components\TextInput::make('option_b')->label('Opsi B')->required(),
                                    Forms\Components\TextInput::make('option_c')->label('Opsi C'), 
                                    Forms\Components\TextInput::make('option_d')->label('Opsi D'), 
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

                        // SETTING DURASI & MINIMAL BACA
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('duration_minutes')
                                    ->label('Estimasi Durasi (Menit)')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Perkiraan waktu yang dibutuhkan user untuk materi ini.'),

                                Forms\Components\TextInput::make('min_viewing_seconds')
                                    ->label('Minimal Waktu Baca (Detik)')
                                    ->numeric()
                                    ->default(0)
                                    ->required(fn (Get $get) => $get('type') === 'pdf')
                                    ->visible(fn (Get $get) => $get('type') === 'pdf')
                                    ->helperText('User tidak bisa klik Selesai sebelum waktu ini habis.'),
                            ]),

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
            // Tombol Import sudah dipindah ke halaman ListLessons, jadi bagian ini bersih.
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
                        'pdf' => 'danger', 
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true), 

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i') 
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'video' => 'Video',
                        'pdf'   => 'PDF Document',
                        'quiz'  => 'Kuis',
                    ])
                    ->label('Tipe Pelajaran'),

                Tables\Filters\SelectFilter::make('modules')
                    ->relationship('modules', 'name')
                    ->label('Module Terkait')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                Tables\Filters\SelectFilter::make('course')
                    ->label('Course Terkait')
                    ->options(fn() => \App\Models\Course::pluck('title', 'id')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
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
            ])
            ->defaultSort('created_at', 'desc'); 
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