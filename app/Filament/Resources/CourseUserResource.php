<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseUserResource\Pages;
use App\Models\CourseUser; // Pakai model Pivot tadi
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CourseUserResource extends Resource
{
    protected static ?string $model = CourseUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static ?string $navigationLabel = 'Laporan Enrollment';

    protected static ?string $pluralLabel = 'Laporan Enrollment';

    protected static ?string $navigationGroup = 'Laporan'; // Grup Menu Baru

    protected static ?int $navigationSort = 1;

    // Kita bikin READ ONLY saja biar jadi Laporan murni
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'active' => 'On Progress',
                        'completed' => 'Selesai',
                    ]),
                // Tambahkan field lain jika ingin bisa edit data pivot
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Nama User
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CourseUser $record) => $record->user->email ?? '-'),

                // 2. Nama Course
                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                // 3. Status
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'active' => 'warning',
                        default => 'gray',
                    }),

                // 4. Progress
                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),

                // 5. Tanggal Enroll
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Enroll')
                    ->dateTime('d M Y')
                    ->sortable(),
                
                // 6. Tanggal Selesai
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tgl Selesai')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('-'),

                // 7. Durasi (Custom Logic)
                Tables\Columns\TextColumn::make('duration')
                    ->label('Durasi Pengerjaan')
                    ->state(function (Model $record): string {
                        if (!$record->completed_at) return '-';
                        
                        return Carbon::parse($record->created_at)
                            ->diffForHumans(Carbon::parse($record->completed_at), [
                                'syntax' => Carbon::DIFF_ABSOLUTE,
                                'parts' => 2,
                            ]);
                    }),
                // 8. Last Accessed
                Tables\Columns\TextColumn::make('last_accessed_at')
                    ->label('Akses Terakhir')
                    ->dateTime('d M Y H:i') // Format tanggal dan jam
                    ->sortable()
                    ->placeholder('Belum pernah diakses'),
            ])
            ->filters([
                // Filter berdasarkan Status
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'On Progress',
                        'completed' => 'Selesai',
                    ]),
                
                // Filter berdasarkan Course
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Filter Course'),

                // Filter Tanggal Enroll
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Enroll Dari'),
                        Forms\Components\DatePicker::make('created_until')->label('Enroll Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\Action::make('detail_progress')
                    ->label('Detail Progress')
                    ->icon('heroicon-m-list-bullet')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Detail Progress: ' . ($record->user->name ?? 'User'))
                    ->modalContent(fn ($record) => view('filament.resources.course-user-resource.detail-progress', [
                        'record' => $record,
                    ]))
                    ->modalSubmitAction(false) // Sembunyikan tombol submit karena ini cuma view
                    ->modalCancelActionLabel('Tutup'),
                // Tables\Actions\EditAction::make(), // Aktifkan jika mau edit
                // Tables\Actions\DeleteAction::make(), // Aktifkan jika mau delete
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
            'index' => Pages\ListCourseUsers::route('/'),
            // Kita matikan create/edit biar jadi report only
            // 'create' => Pages\CreateCourseUser::route('/create'),
            // 'edit' => Pages\EditCourseUser::route('/{record}/edit'),
        ];
    }

    // --- PENTING: BATASI AKSES DATA (SCOPING) ---
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        // Super Admin lihat semua
        if ($user->hasRole('super_admin')) {
            return $query;
        }

        // Middle Admin HANYA lihat enrollment dari User yg ada di Main Dealer dia
        if ($user->hasRole('middle_admin')) {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('main_dealer_id', $user->main_dealer_id);
                // Opsional: Sembunyikan data Super Admin dari laporan
                $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'super_admin'));
            });
        }

        return $query->whereRaw('1 = 0');
    }
}