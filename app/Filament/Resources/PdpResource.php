<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PdpResource\Pages;
use App\Models\Pdp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PdpResource extends Resource
{
    protected static ?string $model = Pdp::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?string $modelLabel = 'Persetujuan PDP';
    protected static ?string $pluralModelLabel = 'Persetujuan PDP';

    // 1. MATIKAN TOMBOL CREATE / TAMBAH BARU
    public static function canCreate(): bool
    {
        return false;
    }

    // (Opsional) MATIKAN TOMBOL DELETE AGAR TIDAK BISA DIHAPUS
    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
{
    return false;
}

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pengaturan Konten PDP')
                    ->description('Kelola teks persetujuan pemrosesan data pribadi yang akan muncul di halaman register.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Modal')
                            ->required()
                            ->maxLength(255)
                            ->default('Persetujuan Pemrosesan Data Pribadi (PDP)'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Konten')
                            ->required()
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold',
                                'italic',
                                'underline',
                                'strike',
                                'bulletList',
                                'orderedList',
                                'h2',
                                'h3',
                                'link',
                                'redo',
                                'undo',
                            ]),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->helperText('Jika diaktifkan, konten ini yang akan ditampilkan ke user.')
                            ->default(true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Pastikan tidak ada DeleteAction di sini
            ])
            ->bulkActions([
                // 2. HAPUS DELETE BULK ACTION AGAR TIDAK BISA DIHAPUS MASSAL
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListPdps::route('/'),
            // 3. HAPUS ROUTE CREATE
            // 'create' => Pages\CreatePdp::route('/create'),
            'edit' => Pages\EditPdp::route('/{record}/edit'),
        ];
    }
}