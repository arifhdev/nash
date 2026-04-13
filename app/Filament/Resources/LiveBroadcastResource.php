<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LiveBroadcastResource\Pages;
use App\Models\LiveBroadcast;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;

class LiveBroadcastResource extends Resource
{
    protected static ?string $model = LiveBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal'; // Ikon sinyal/live
    protected static ?string $navigationLabel = 'Live Broadcast';
    protected static ?string $modelLabel = 'Live Broadcast';
    protected static ?string $pluralModelLabel = 'Live Broadcasts';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Tayangan')
                    ->description('Masukkan detail informasi dan link YouTube untuk broadcast ini.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Judul Tayangan')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('youtube_url')
                            ->label('Link YouTube')
                            ->url()
                            ->placeholder('Contoh: https://www.youtube.com/watch?v=...')
                            ->helperText('Anda bisa memasukkan link format apa saja (youtube.com, youtu.be, youtube.com/live).')
                            ->required()
                            ->columnSpanFull(),

                        Select::make('status')
                            ->label('Status Tayangan')
                            ->options([
                                'upcoming' => 'Belum Mulai (Upcoming)',
                                'live' => 'Sedang Live! (Live)',
                                'ended' => 'Selesai (Ended / VOD)',
                            ])
                            ->default('upcoming')
                            ->native(false)
                            ->required(),

                        DateTimePicker::make('scheduled_at')
                            ->label('Jadwal Tayang (Opsional)')
                            ->native(false),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Tayangan')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'upcoming' => 'warning',
                        'live' => 'danger', // Warna merah untuk menandakan LIVE
                        'ended' => 'success',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'upcoming' => 'Upcoming',
                        'live' => '🔴 LIVE',
                        'ended' => 'Ended',
                    }),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'upcoming' => 'Upcoming',
                        'live' => 'Live',
                        'ended' => 'Ended',
                    ]),
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
            ->emptyStateHeading('Belum ada Broadcast')
            ->emptyStateDescription('Mulai tambahkan link YouTube live pertama Anda.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLiveBroadcasts::route('/'),
            'create' => Pages\CreateLiveBroadcast::route('/create'),
            'edit' => Pages\EditLiveBroadcast::route('/{record}/edit'),
        ];
    }
}