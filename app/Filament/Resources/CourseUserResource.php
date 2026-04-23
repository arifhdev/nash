<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CourseUserResource\Pages;
use App\Models\CourseUser;
use App\Models\Division;
use App\Models\Position;
use App\Models\MainDealer;
use App\Models\Dealer;
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

    protected static ?string $navigationGroup = 'Laporan'; 

    protected static ?int $navigationSort = 1;

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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama User')
                    ->searchable()
                    ->sortable()
                    ->description(fn (CourseUser $record) => $record->user->email ?? '-'),

                Tables\Columns\TextColumn::make('course.title')
                    ->label('Course')
                    ->searchable()
                    ->sortable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'active' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->numeric()
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Enroll')
                    ->dateTime('d M Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Tgl Selesai')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->placeholder('-'),

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

                Tables\Columns\TextColumn::make('last_accessed_at')
                    ->label('Akses Terakhir')
                    ->dateTime('d M Y H:i') 
                    ->sortable()
                    ->placeholder('Belum pernah diakses'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'On Progress',
                        'completed' => 'Selesai',
                    ]),
                
                Tables\Filters\SelectFilter::make('course_id')
                    ->relationship('course', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Filter Course'),

                Tables\Filters\SelectFilter::make('division_id')
                    ->label('Divisi')
                    ->options(Division::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', fn($q) => $q->where('division_id', $value))
                        );
                    }),

                Tables\Filters\SelectFilter::make('position_id')
                    ->label('Jabatan')
                    ->options(Position::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', fn($q) => $q->where('position_id', $value))
                        );
                    }),

                Tables\Filters\SelectFilter::make('main_dealer_id')
                    ->label('Main Dealer')
                    ->options(MainDealer::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', fn($q) => $q->where('main_dealer_id', $value))
                        );
                    }),

                Tables\Filters\SelectFilter::make('dealer_id')
                    ->label('Dealer')
                    ->options(Dealer::pluck('name', 'id'))
                    ->searchable()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $value): Builder => $query->whereHas('user', fn($q) => $q->where('dealer_id', $value))
                        );
                    }),

                Tables\Filters\Filter::make('enrollment_date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Enroll Dari'),
                        Forms\Components\DatePicker::make('created_until')->label('Enroll Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('course_user.created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('course_user.created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        
                        if ($data['created_from'] ?? null) {
                            $indicators['created_from'] = 'Enroll Dari: ' . Carbon::parse($data['created_from'])->format('d M Y');
                        }
                        
                        if ($data['created_until'] ?? null) {
                            $indicators['created_until'] = 'Enroll Sampai: ' . Carbon::parse($data['created_until'])->format('d M Y');
                        }

                        return $indicators;
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
                    ->modalSubmitAction(false) 
                    ->modalCancelActionLabel('Tutup'),
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
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        if ($user->hasRole('middle_admin')) {
            return $query->whereHas('user', function ($q) use ($user) {
                $q->where('main_dealer_id', $user->main_dealer_id);
                $q->whereDoesntHave('roles', fn($r) => $r->where('name', 'super_admin'));
            });
        }

        return $query->whereRaw('1 = 0');
    }
}