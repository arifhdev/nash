<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $title = 'Riwayat Kursus (LMS)';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Biasanya history course bersifat Read Only di sini,
                // tapi jika mau edit manual status user di course:
                Forms\Components\Select::make('status')
                    ->options([
                        'started' => 'On Progress',
                        'completed' => 'Selesai',
                    ])
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                // 1. Nama Course
                Tables\Columns\TextColumn::make('title')
                    ->label('Nama Kursus')
                    ->searchable()
                    ->sortable(),

                // 2. Tanggal Enroll (Diambil dari pivot created_at)
                Tables\Columns\TextColumn::make('pivot.created_at')
                    ->label('Tanggal Enroll')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                // 3. Status Progress
                Tables\Columns\TextColumn::make('pivot.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'started' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'completed' => 'Selesai',
                        'started' => 'Sedang Berjalan',
                        default => $state,
                    }),

                // 4. Progress Persen (Opsional)
                Tables\Columns\TextColumn::make('pivot.progress_percent')
                    ->label('Progress')
                    ->suffix('%')
                    ->numeric(),

                // 5. Tanggal Selesai
                Tables\Columns\TextColumn::make('pivot.completed_at')
                    ->label('Selesai Pada')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('-'),

                // 6. LOGIC DURASI: (Selesai - Enroll)
                Tables\Columns\TextColumn::make('duration')
                    ->label('Lama Penyelesaian')
                    ->state(function (Model $record): string {
                        // $record di sini adalah Course, tapi punya akses ke pivot
                        $enrollDate = $record->pivot->created_at;
                        $completeDate = $record->pivot->completed_at;

                        if (!$completeDate) {
                            return '-'; // Belum selesai
                        }

                        // Hitung selisih
                        return Carbon::parse($enrollDate)
                            ->diffForHumans(Carbon::parse($completeDate), [
                                'syntax' => Carbon::DIFF_ABSOLUTE, // "3 days", bukan "3 days ago"
                                'parts' => 2, // "3 days 4 hours" (detail)
                            ]);
                    }),
            ])
            ->filters([
                // Filter Status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'started' => 'On Progress',
                        'completed' => 'Selesai',
                    ])
                    // Karena kolomnya di pivot table, query-nya beda dikit:
                    ->query(function ($query, array $data) {
                        if ($data['value']) {
                            $query->wherePivot('status', $data['value']);
                        }
                    }),
            ])
            ->headerActions([
                // Jika admin boleh mendaftarkan user ke course secara manual:
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect(),
                        // Field tambahan saat attach
                        Forms\Components\Select::make('status')
                            ->options(['started' => 'Started', 'completed' => 'Completed'])
                            ->default('started')
                            ->required(),
                    ]),
            ])
            ->actions([
                // Edit pivot data (misal benerin status manual)
                Tables\Actions\EditAction::make(), 
                // Hapus enrollment
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}