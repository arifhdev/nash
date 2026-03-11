<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookletResource\Pages;
use App\Models\Booklet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BookletResource extends Resource
{
    protected static ?string $model = Booklet::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open'; // Icon Buku
    
    protected static ?string $navigationGroup = 'Content Management'; // Sesuai request Bos
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
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
                    ])->columns(2),

                Forms\Components\Section::make('File & Media')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Cover / Thumbnail (Opsional)')
                            ->image()
                            ->directory('booklets/covers')
                            ->imageEditor(),

                        Forms\Components\FileUpload::make('pdf_file')
                            ->label('File PDF')
                            ->acceptedFileTypes(['application/pdf']) // Kunci hanya PDF
                            ->directory('booklets/pdfs')
                            ->required()
                            ->downloadable()
                            ->openable(),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Matikan jika booklet belum siap dipublikasi.'),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->defaultImageUrl(url('images/placeholder.jpg')) // Pastikan punya placeholder kalau gak ada cover
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
            //
        ];
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