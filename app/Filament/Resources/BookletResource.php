<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookletResource\Pages;
use App\Models\Booklet;
use App\Models\MainDealer;
use App\Models\Position; 
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BookletResource extends Resource
{
    protected static ?string $model = Booklet::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open'; 
    protected static ?string $navigationGroup = 'Content Management'; 
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Booklet Tabs')
                    ->tabs([
                        // TAB 1: DATA BOOKLET
                        Forms\Components\Tabs\Tab::make('Data Booklet')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\Section::make('Informasi Booklet')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Booklet')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->live(onBlur: true)
                                                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                                                Forms\Components\TextInput::make('slug')
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->unique(Booklet::class, 'slug', ignoreRecord: true),

                                                Forms\Components\Textarea::make('description')
                                                    ->label('Deskripsi Singkat')
                                                    ->rows(4)
                                                    ->columnSpanFull(),
                                            ])->columns(2)->columnSpan(2),

                                        Forms\Components\Section::make('File & Media')
                                            ->schema([
                                                Forms\Components\FileUpload::make('cover_image')
                                                    ->label('Cover / Thumbnail (Opsional)')
                                                    ->image()
                                                    ->directory('booklets/covers')
                                                    ->imageEditor(),

                                                Forms\Components\FileUpload::make('pdf_file')
                                                    ->label('File PDF')
                                                    ->acceptedFileTypes(['application/pdf']) 
                                                    ->directory('booklets/pdfs')
                                                    ->required()
                                                    ->downloadable()
                                                    ->openable(),
                                                    
                                                Forms\Components\Toggle::make('is_active')
                                                    ->label('Status Aktif')
                                                    ->default(true)
                                                    ->helperText('Matikan jika booklet belum siap dipublikasi.'),
                                            ])->columnSpan(1),
                                    ]),
                                
                                // REPEATER VIDEO YOUTUBE
                                Forms\Components\Section::make('Video YouTube Terkait')
                                    ->description('Tambahkan link video YouTube sebagai pelengkap materi booklet.')
                                    ->schema([
                                        Forms\Components\Repeater::make('youtube_videos')
                                            ->label('')
                                            ->addActionLabel('Tambah Video')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label('Judul Video')
                                                    ->required()
                                                    ->maxLength(255),
                                                    
                                                Forms\Components\TextInput::make('url')
                                                    ->label('Link YouTube')
                                                    ->url() 
                                                    ->required()
                                                    ->helperText('Contoh: https://www.youtube.com/watch?v=dQw4w9WgXcQ'),
                                            ])
                                            ->columns(2)
                                            ->defaultItems(0) // Tidak muncul form kosong secara default
                                            ->reorderable()   // User bisa mengatur urutan video
                                            ->collapsible()
                                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null), // Menampilkan judul di header item jika di-collapse
                                    ]),
                            ]),

                        // TAB 2: ASSIGN AKSES (GABUNGAN TIPE USER, JABATAN & MAIN DEALER)
                        Forms\Components\Tabs\Tab::make('Assign Akses')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                
                                // 1. FILTER TIPE USER
                                Forms\Components\Section::make('Filter Tipe User')
                                    ->description('Centang tipe user yang berhak melihat booklet ini.')
                                    ->schema([
                                        Forms\Components\CheckboxList::make('user_type_filter')
                                            ->label('')
                                            ->options([
                                                'ahm' => 'Karyawan AHM',
                                                'main_dealer' => 'Karyawan Main Dealer',
                                                'dealer' => 'Karyawan Dealer',
                                            ])
                                            ->live()
                                            ->dehydrated(false) // Field virtual, tidak disimpan ke DB
                                            ->afterStateHydrated(function (Forms\Components\CheckboxList $component, ?Booklet $record) {
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

                                // 2A. JABATAN AHM
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
                                                    ->when($get('ahm_division_filter'), fn ($q, $divisi) => $q->where('positions.divisi', $divisi))
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name}" . ($record->divisi ? " ({$record->divisi})" : ""))
                                            ->searchable()
                                            ->bulkToggleable()
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'ahm')->pluck('id')->toArray()),
                                    ]),

                                // 2B. JABATAN MAIN DEALER
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
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'main_dealer')->pluck('id')->toArray()),
                                    ]),

                                // 2C. JABATAN DEALER
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
                                            ->columns(3)
                                            ->gridDirection('row')
                                            ->default(fn () => Position::where('user_type', 'dealer')->pluck('id')->toArray()),
                                    ]),

                                // 3. ASSIGN MAIN DEALER
                                Forms\Components\Section::make('Assign Main Dealer')
                                    ->description('Tentukan Main Dealer mana saja yang berhak mengakses Booklet ini.')
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
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->defaultImageUrl(url('images/placeholder.jpg')) 
                    ->circular(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('pdf_file')
                    ->label('File')
                    ->formatStateUsing(fn ($state) => 'Lihat PDF')
                    ->url(fn ($record) => asset('storage/' . $record->pdf_file))
                    ->openUrlInNewTab()
                    ->color('info')
                    ->icon('heroicon-o-document-text'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            // Kosong karena kita pakai Repeater, bukan Relation Manager
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooklets::route('/'),
            'create' => Pages\CreateBooklet::route('/create'),
            'edit' => Pages\EditBooklet::route('/{record}/edit'),
        ];
    }
}