<?php

namespace App\Filament\Resources\KlhnEventResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextInputColumn; // Untuk edit caption langsung di tabel
use Filament\Forms\Components\FileUpload;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';
    protected static ?string $title = 'Galeri Foto';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('image')
                    ->label('Foto')
                    ->image()
                    ->directory('klhn-gallery')
                    ->required(),
                Forms\Components\TextInput::make('caption')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('image')
            ->columns([
                ImageColumn::make('image')
                    ->label('Foto')
                    ->size(100)
                    ->square(),
                
                // MAGIC UX: Admin bisa ketik caption langsung di row tabel tanpa perlu klik tombol Edit!
                TextInputColumn::make('caption')
                    ->label('Caption Foto')
                    ->placeholder('Ketik caption di sini...'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // INI ADALAH FITUR BULK UPLOAD
                Tables\Actions\Action::make('bulk_upload')
                    ->label('Bulk Upload Foto')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        FileUpload::make('images')
                            ->label('Pilih Banyak Foto')
                            ->multiple() // Mengizinkan pilih banyak file sekaligus
                            ->image()
                            ->directory('klhn-gallery')
                            ->reorderable()
                            ->required(),
                    ])
                    ->action(function (array $data, $livewire) {
                        // Looping setiap foto yang diupload dan simpan ke database
                        foreach ($data['images'] as $imagePath) {
                            $livewire->getOwnerRecord()->photos()->create([
                                'image' => $imagePath,
                                'caption' => null, // Caption diisi nanti lewat tabel
                            ]);
                        }
                    }),
                    
                Tables\Actions\CreateAction::make()->label('Upload 1 Foto'),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}