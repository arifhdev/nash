<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Testimonial Detail')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('testimonials')
                            ->avatar()
                            ->required(),
                        
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('role')
                                ->label('Jabatan/Posisi')
                                ->placeholder('Contoh: Mekanik AHASS')
                                ->required(),
                        ]),

                        Forms\Components\Textarea::make('quote')
                            ->label('Pesan/Komentar')
                            ->required()
                            ->rows(3),

                        Forms\Components\Select::make('rating')
                            ->options([
                                5 => '5 Bintang',
                                4 => '4 Bintang',
                                3 => '3 Bintang',
                                2 => '2 Bintang',
                                1 => '1 Bintang',
                            ])->default(5),
                            
                        Forms\Components\Toggle::make('is_active')->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('role')->color('gray'),
                Tables\Columns\IconColumn::make('rating')
                    ->icon('heroicon-s-star')
                    ->color('warning'),
                Tables\Columns\ToggleColumn::make('is_active'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}