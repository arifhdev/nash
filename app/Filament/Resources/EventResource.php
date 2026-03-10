<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Kolom Kiri (Utama)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Event Details')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),

                                Forms\Components\RichEditor::make('content')
                                    ->columnSpanFull(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                // Kolom Kanan (Sidebar)
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Status & Image')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->required()
                                    ->default(true),

                                Forms\Components\FileUpload::make('thumbnail')
                                    ->image()
                                    ->directory('events') // Folder penyimpanan
                                    ->required()
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make('Schedule')
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->required(),
                                
                                Forms\Components\DateTimePicker::make('end_date'),
                                
                                Forms\Components\TextInput::make('location')
                                    ->maxLength(255),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->square(),
                
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('location')
                    ->icon('heroicon-m-map-pin')
                    ->limit(20),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Active'),
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
            ])
            ->defaultSort('start_date', 'desc'); // Event terbaru di atas
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}