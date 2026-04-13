<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KlhnEventResource\Pages;
use App\Filament\Resources\KlhnEventResource\RelationManagers;
use App\Models\KlhnEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;

class KlhnEventResource extends Resource
{
    protected static ?string $model = KlhnEvent::class;

    // Konfigurasi icon dan label di Sidebar Filament
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'KLHN';
    protected static ?string $modelLabel = 'KLHN';
    protected static ?string $pluralModelLabel = 'KLHN';
    protected static ?string $navigationGroup = 'Content Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Event')
                    ->description('Masukkan detail informasi mengenai event KLHN.')
                    ->schema([
                        TextInput::make('title')
                            ->label('Nama Event')
                            ->required()
                            ->maxLength(255),
                        
                        DatePicker::make('event_date')
                            ->label('Tanggal Event')
                            ->native(false) // Menggunakan datepicker Filament yang lebih modern
                            ->displayFormat('d/m/Y'),
                            
                        Textarea::make('description')
                            ->label('Deskripsi Event')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),
                    
                // Section Galeri dihapus dari sini karena form upload-nya
                // akan berada di halaman Edit (via Relation Manager)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Tanggal Event')
                    ->date('d F Y') // Format: 02 April 2026
                    ->sortable(),

                // Menggunakan counts() untuk menghitung jumlah relasi hasMany
                Tables\Columns\TextColumn::make('photos_count')
                    ->counts('photos')
                    ->label('Total Foto')
                    ->badge() 
                    ->color('success'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Anda bisa menambahkan filter tanggal event di sini jika diperlukan
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
            ->emptyStateHeading('Belum ada Event KLHN')
            ->emptyStateDescription('Buat event pertama Anda dengan mengklik tombol di bawah.');
    }

    public static function getRelations(): array
    {
        return [
            // Daftarkan Relation Manager di sini
            RelationManagers\PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKlhnEvents::route('/'),
            'create' => Pages\CreateKlhnEvent::route('/create'),
            'edit' => Pages\EditKlhnEvent::route('/{record}/edit'),
        ];
    }
}