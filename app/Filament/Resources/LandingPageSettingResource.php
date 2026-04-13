<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandingPageSettingResource\Pages;
use App\Models\LandingPageSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LandingPageSettingResource extends Resource
{
    protected static ?string $model = LandingPageSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?string $navigationLabel = 'Landing Page Settings';
    protected static ?string $modelLabel = 'Setting';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('key')
                ->label('Setting Key')
                ->required()
                ->disabled() 
                ->columnSpanFull(),

            Forms\Components\Section::make('Section: Mengapa Bergabung')
                ->description('Kelola teks promosi dan benefit di halaman depan.')
                ->schema([
                    // HAPUS "payload." di depan nama field
                    Forms\Components\TextInput::make('title') 
                        ->label('Judul Section')
                        ->required(),
                    
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi/Sub-judul')
                        ->rows(3)
                        ->required(),

                    Forms\Components\Repeater::make('benefits')
                        ->label('Daftar Keuntungan (List)')
                        ->schema([
                            Forms\Components\TextInput::make('text')
                                ->label('Poin Benefit')
                                ->required(),
                        ])
                        ->collapsible()
                        ->itemLabel(fn (array $state): ?string => $state['text'] ?? null),

                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\TextInput::make('cta_text')
                                ->label('Teks Tombol (CTA)')
                                ->required(),
                            Forms\Components\TextInput::make('cta_url')
                                ->label('Link Tombol')
                                ->required(),
                        ]),
                ])->statePath('payload'), // Cukup di sini saja kita sebut "payload"
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->description(fn (LandingPageSetting $record): string => match($record->key) {
                        'why_join_section' => 'Mengatur bagian "Mengapa Bergabung" di Landing Page',
                        default => 'Pengaturan Umum',
                    }),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime('d M Y H:i'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    // ============================================================
    // PERMISSION CONTROL: MATIKAN CREATE & DELETE
    // ============================================================
    
    public static function canCreate(): bool
    {
        return false; // Menghilangkan tombol "New Setting"
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return false; // Mencegah admin menghapus setting yang sudah ada
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLandingPageSettings::route('/'),
            'edit' => Pages\EditLandingPageSetting::route('/{record}/edit'),
        ];
    }
}