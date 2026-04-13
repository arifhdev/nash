<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseResource\Pages;
use App\Filament\Resources\CourseResource\RelationManagers;
use App\Models\Course;
use App\Models\MainDealer;
use App\Models\Position;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
                                                    ->maxLength(255)
                                                    ->unique(ignoreRecord: true),

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

                                                Forms\Components\RichEditor::make('description')
                                                    ->label('Deskripsi Singkat')
                                                    ->columnSpanFull()
                                                    ->toolbarButtons([
                                                        'blockquote',
                                                        'bold',
                                                        'bulletList',
                                                        'h2',
                                                        'h3',
                                                        'italic',
                                                        'link',
                                                        'orderedList',
                                                        'redo',
                                                        'strike',
                                                        'underline',
                                                        'undo',
                                                    ]),
                                            ])->columns(2)->columnSpan(2),

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

                                                Forms\Components\TextInput::make('points_reward')
                                                    ->label('Point (Currency)')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->prefixIcon('heroicon-o-banknotes'),

                                                Forms\Components\TextInput::make('xp_reward')
                                                    ->label('XP (Life-time)')
                                                    ->numeric()
                                                    ->default(0)
                                                    ->prefixIcon('heroicon-o-bolt'),

                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Publikasikan')
                                                    ->default(true)
                                                    ->onColor('success'),
                                                
                                                Forms\Components\Toggle::make('has_certificate')
                                                    ->label('Dapat Sertifikat')
                                                    ->default(true)
                                                    ->onColor('success'),

                                                // --- PENGATURAN BARU ---
                                                Forms\Components\Toggle::make('require_sequential')
                                                    ->label('Materi Wajib Berurutan')
                                                    ->helperText('Matikan jika murid bebas mengakses materi secara acak.')
                                                    ->default(true)
                                                    ->onColor('success'),
                                            ])->columnSpan(1),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 2: ASSIGN AKSES
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('Assign Akses')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Section::make('Filter Tipe User')
                                    ->description('Centang tipe user yang diizinkan mengakses course ini.')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('user_type_filter')
                                            ->label('')
                                            ->options([
                                                'ahm' => 'Karyawan AHM',
                                                'main_dealer' => 'Karyawan Main Dealer',
                                                'dealer' => 'Karyawan Dealer',
                                            ])
                                            ->live()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function (Forms\Components\CheckboxList $component, ?Course $record) {
                                                if (! $record || ! $record->exists) {
                                                    $component->state(['ahm', 'main_dealer', 'dealer']);
                                                    return;
                                                }

                                                $state = [];
                                                if ($record->ahmPositions()->exists()) $state[] = 'ahm';
                                                if ($record->mdPositions()->exists()) $state[] = 'main_dealer';
                                                if ($record->dealerPositions()->exists()) $state[] = 'dealer';
                                                
                                                if (empty($state) && $record->positions()->count() === 0) {
                                                     $state = ['ahm', 'main_dealer', 'dealer'];
                                                }

                                                $component->state($state);
                                            })
                                            ->columns(3),
                                    ]),

                                Forms\Components\Section::make('Jabatan AHM')
                                    ->visible(fn (Forms\Get $get) => in_array('ahm', $get('user_type_filter') ?? []))
                                    ->schema([
                                        Forms\Components\Select::make('ahm_division_filter')
                                            ->label('Filter Berdasarkan Divisi AHM')
                                            ->options(fn () => Position::where('user_type', 'ahm')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('ahmPositions')
                                            ->label('Pilih Jabatan AHM')
                                            ->relationship(
                                                name: 'ahmPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query
                                                    // Mencegah error ambiguous dengan prefix positions.
                                                    ->when($get('ahm_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi))
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->live()
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'ahm')->pluck('id')->toArray()),
                                    ]),

                                Forms\Components\Section::make('Jabatan Main Dealer')
                                    ->visible(fn (Forms\Get $get) => in_array('main_dealer', $get('user_type_filter') ?? []))
                                    ->schema([
                                        Forms\Components\Select::make('md_division_filter')
                                            ->label('Filter Berdasarkan Divisi Main Dealer')
                                            ->options(fn () => Position::where('user_type', 'main_dealer')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('mdPositions')
                                            ->label('Pilih Jabatan Main Dealer')
                                            ->relationship(
                                                name: 'mdPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query
                                                    ->when($get('md_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi))
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->live()
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'main_dealer')->pluck('id')->toArray()),
                                    ]),

                                Forms\Components\Section::make('Jabatan Dealer')
                                    ->visible(fn (Forms\Get $get) => in_array('dealer', $get('user_type_filter') ?? []))
                                    ->schema([
                                        Forms\Components\Select::make('dealer_division_filter')
                                            ->label('Filter Berdasarkan Divisi Dealer')
                                            ->options(fn () => Position::where('user_type', 'dealer')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('dealerPositions')
                                            ->label('Pilih Jabatan Dealer')
                                            ->relationship(
                                                name: 'dealerPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Forms\Get $get, Builder $query) => $query
                                                    ->when($get('dealer_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi))
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->live()
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'dealer')->pluck('id')->toArray()),
                                    ]),

                                Forms\Components\Section::make('Assign Main Dealer')
                                    ->description('Tentukan Main Dealer mana saja yang berhak mengakses course ini.')
                                    ->visible(fn (Forms\Get $get) => count(array_intersect(['main_dealer', 'dealer'], $get('user_type_filter') ?? [])) > 0)
                                    ->schema([
                                        Forms\Components\CheckboxList::make('mainDealers')
                                            ->label('Pilih Main Dealer')
                                            ->relationship(name: 'mainDealers', titleAttribute: 'name')
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->code ? " - {$record->code}" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => MainDealer::pluck('id')->toArray()),
                                    ]),
                            ]),

                        // ==========================================
                        // TAB 3: ASSIGN WAJIB
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('Assign Jabatan Wajib')
                            ->icon('heroicon-o-exclamation-circle')
                            ->schema([
                                // WAJIB AHM
                                Forms\Components\Section::make('Wajib - Jabatan AHM')
                                    ->description('Pilih jabatan AHM yang diwajibkan mengikuti course ini.')
                                    ->visible(fn (Forms\Get $get) => in_array('ahm', $get('user_type_filter') ?? []) && !empty($get('ahmPositions')))
                                    ->schema([
                                        Forms\Components\Select::make('ahm_mandatory_division_filter')
                                            ->label('Filter Berdasarkan Divisi AHM')
                                            ->options(fn () => Position::where('user_type', 'ahm')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('ahmMandatoryPositions')
                                            ->label('')
                                            ->relationship(
                                                name: 'ahmMandatoryPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                                    $allowedIds = (array) $get('ahmPositions');
                                                    if (empty($allowedIds)) return $query->whereRaw('1 = 0');
                                                    
                                                    return $query->whereIn('positions.id', $allowedIds)
                                                                 ->when($get('ahm_mandatory_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi));
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->columns(3)
                                            ->gridDirection('row'),
                                    ]),

                                // WAJIB MAIN DEALER
                                Forms\Components\Section::make('Wajib - Jabatan Main Dealer')
                                    ->description('Pilih jabatan Main Dealer yang diwajibkan mengikuti course ini.')
                                    ->visible(fn (Forms\Get $get) => in_array('main_dealer', $get('user_type_filter') ?? []) && !empty($get('mdPositions')))
                                    ->schema([
                                        Forms\Components\Select::make('md_mandatory_division_filter')
                                            ->label('Filter Berdasarkan Divisi Main Dealer')
                                            ->options(fn () => Position::where('user_type', 'main_dealer')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('mdMandatoryPositions')
                                            ->label('')
                                            ->relationship(
                                                name: 'mdMandatoryPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                                    $allowedIds = (array) $get('mdPositions');
                                                    if (empty($allowedIds)) return $query->whereRaw('1 = 0');
                                                    
                                                    return $query->whereIn('positions.id', $allowedIds)
                                                                 ->when($get('md_mandatory_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi));
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->columns(3)
                                            ->gridDirection('row'),
                                    ]),

                                // WAJIB DEALER
                                Forms\Components\Section::make('Wajib - Jabatan Dealer')
                                    ->description('Pilih jabatan Dealer yang diwajibkan mengikuti course ini.')
                                    ->visible(fn (Forms\Get $get) => in_array('dealer', $get('user_type_filter') ?? []) && !empty($get('dealerPositions')))
                                    ->schema([
                                        Forms\Components\Select::make('dealer_mandatory_division_filter')
                                            ->label('Filter Berdasarkan Divisi Dealer')
                                            ->options(fn () => Position::where('user_type', 'dealer')->distinct()->whereNotNull('divisi')->pluck('divisi', 'divisi'))
                                            ->placeholder('Semua Divisi')
                                            ->live()
                                            ->dehydrated(false),

                                        Forms\Components\CheckboxList::make('dealerMandatoryPositions')
                                            ->label('')
                                            ->relationship(
                                                name: 'dealerMandatoryPositions', 
                                                titleAttribute: 'name',
                                                modifyQueryUsing: function (Builder $query, Forms\Get $get) {
                                                    $allowedIds = (array) $get('dealerPositions');
                                                    if (empty($allowedIds)) return $query->whereRaw('1 = 0');
                                                    
                                                    return $query->whereIn('positions.id', $allowedIds)
                                                                 ->when($get('dealer_mandatory_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi));
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->columns(3)
                                            ->gridDirection('row'),
                                    ]),

                                // Peringatan Kosong
                                Forms\Components\Placeholder::make('info_wajib')
                                    ->label('')
                                    ->content('⚠️ Silakan pilih minimal 1 jabatan di tab "Assign Akses" terlebih dahulu untuk memunculkan opsi Kewajiban.')
                                    ->visible(fn (Forms\Get $get) => empty($get('ahmPositions')) && empty($get('mdPositions')) && empty($get('dealerPositions'))),
                            ]),

                        // ==========================================
                        // TAB 4: PRASYARAT & RELASI (Course Bersyarat)
                        // ==========================================
                        Forms\Components\Tabs\Tab::make('Prasyarat Course')
                            ->icon('heroicon-o-link')
                            ->schema([
                                Forms\Components\Section::make('Learning Path')
                                    ->description('Tentukan course mana saja yang wajib diselesaikan (Completed) oleh user sebelum bisa mengakses course ini.')
                                    ->schema([
                                        Forms\Components\Select::make('prerequisites')
                                            ->label('Pilih Course Prasyarat')
                                            ->relationship(
                                                name: 'prerequisites', 
                                                titleAttribute: 'title',
                                                // Mencegah course memilih dirinya sendiri sebagai prasyarat
                                                modifyQueryUsing: fn (Builder $query, ?Model $record) => $query
                                                    ->when($record, fn ($q) => $q->where('courses.id', '!=', $record->id))
                                            )
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Cari course...')
                                            ->hint('Kosongkan jika course ini dapat diakses langsung secara mandiri.'),
                                    ]),
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

                Tables\Columns\TextColumn::make('modules_count')
                    ->counts('modules')
                    ->label('Jml. Modul')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('prerequisites_count')
                    ->counts('prerequisites')
                    ->label('Prasyarat')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('points_reward')
                    ->label('Poin')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),

                // --- KOLOM BARU DI TABEL ---
                Tables\Columns\ToggleColumn::make('require_sequential')
                    ->label('Wajib Urut'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
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
            ])
            ->defaultSort('created_at', 'desc'); 
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ModulesRelationManager::class,
        ];
    }

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