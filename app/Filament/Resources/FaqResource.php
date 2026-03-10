<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    // ICON & GROUP
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 3; // Urutan di sidebar

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('FAQ Content')
                    ->schema([
                        Forms\Components\TextInput::make('question')
                            ->label('Pertanyaan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('answer') // Pakai Textarea agar bersih, atau RichEditor jika butuh bold/link
                            ->label('Jawaban')
                            ->required()
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('sort')
                                    ->label('Urutan Tampil')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('Angka semakin kecil, semakin di atas.'),
                                
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Tampilkan di Website')
                                    ->default(true)
                                    ->inline(false),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort')
                    ->label('Urut')
                    ->sortable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('answer')
                    ->label('Jawaban')
                    ->limit(50)
                    ->color('gray'),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Aktif'),
            ])
            ->defaultSort('sort', 'asc')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}