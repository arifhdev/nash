<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LessonReportResource\Pages;
use App\Models\LessonUser;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LessonReportResource extends Resource
{
    protected static ?string $model = LessonUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Lessons';

    protected static ?string $pluralModelLabel = 'Laporan Lessons';

    protected static ?int $navigationSort = 3;

    // Matikan Create/Edit agar murni menjadi Read-Only Report
    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_accessed_at', 'desc')
            ->columns([
                // 1. Honda ID
                Tables\Columns\TextColumn::make('user.honda_id')
                    ->label('Honda ID')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                // 2. Nama User
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable(),

                // 3. Nama Lesson
                Tables\Columns\TextColumn::make('lesson.title') // Ganti .name jika di DB namanya name
                    ->label('Lesson')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->description(fn (LessonUser $record): string => 'Course: ' . ($record->course->title ?? '-')),

                // 4. Count View (Status Buka)
                Tables\Columns\TextColumn::make('count_view')
                    ->label('Total View')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // 5. Count Completed (Status Selesai)
                Tables\Columns\TextColumn::make('count_completed')
                    ->label('Total Completed')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                // 6. Last View Date
                Tables\Columns\TextColumn::make('last_accessed_at')
                    ->label('Last View Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),

                // 7. Last Completed Date
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Last Completed Date')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('-'),
            ])
            ->filters([
                // Filter berdasarkan Status Selesai
                Tables\Filters\Filter::make('is_completed')
                    ->label('Hanya yang sudah selesai')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('completed_at')),
                
                // Filter berdasarkan Course
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'title') // Ganti 'name' jika kolomnya name
                    ->label('Filter Course')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // Matikan jika tidak boleh dihapus
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageLessonReports::route('/'),
        ];
    }
}