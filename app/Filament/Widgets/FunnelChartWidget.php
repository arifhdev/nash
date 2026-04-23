<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use App\Models\MainDealer;
use App\Models\Dealer;
use App\Models\Course;
use Illuminate\Support\Facades\DB;

class FunnelChartWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.funnel-chart-widget';
    protected int | string | array $columnSpan = 'full';

    public ?array $filterData = [];

    public function mount(): void
    {
        // Set Default dari 1 Jan 2026 sampai Hari Ini
        $this->form->fill([
            'date_start' => '2026-01-01',
            'date_end' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(7)->schema([ // Grid diubah ke 7 agar proporsional dengan filter Course
                    DatePicker::make('date_start')->label('Mulai')->live(),
                    DatePicker::make('date_end')->label('Selesai')->live(),
                    
                    Select::make('course_id')
                        ->label('Course')
                        ->options(Course::pluck('title', 'id'))
                        ->placeholder('Pilih')
                        ->searchable()
                        ->live(),

                    Select::make('division_id')
                        ->label('Divisi')
                        ->options(Division::pluck('name', 'id'))
                        ->placeholder('Pilih')
                        ->searchable()
                        ->live(),

                    Select::make('position_id')
                        ->label('Jabatan')
                        ->options(Position::pluck('name', 'id'))
                        ->placeholder('Pilih')
                        ->searchable()
                        ->live(),

                    Select::make('main_dealer_id')
                        ->label('Main Dealer')
                        ->options(MainDealer::pluck('name', 'id'))
                        ->placeholder('Pilih')
                        ->searchable()
                        ->live(),

                    Select::make('dealer_id')
                        ->label('Dealer')
                        ->options(Dealer::pluck('name', 'id'))
                        ->placeholder('Pilih')
                        ->searchable()
                        ->live(),
                ])
            ])
            ->statePath('filterData');
    }

    protected function getViewData(): array
    {
        $filters = $this->filterData;
        $start = ($filters['date_start'] ?? '2026-01-01') . ' 00:00:00';
        $end = ($filters['date_end'] ?? now()->format('Y-m-d')) . ' 23:59:59';

        // --- 1. TOTAL & REGISTERED (Hanya yang Aktif di Whitelist) ---
        $tables = ['honda_id_verifications', 'md_id_verifications', 'trainer_id_verifications'];
        
        $totalUsers = 0;
        $registeredUsers = 0;

        foreach ($tables as $table) {
            $query = DB::table($table)->where($table . '.is_active', 1);

            if (!empty($filters['main_dealer_id'])) {
                $query->where($table . '.main_dealer_id', $filters['main_dealer_id']);
            }
            if (!empty($filters['dealer_id'])) {
                if ($table === 'honda_id_verifications') {
                    $query->where($table . '.dealer_id', $filters['dealer_id']);
                } else {
                    $query->whereRaw('1 = 0'); 
                }
            }
            if (!empty($filters['position_id'])) {
                $query->where($table . '.position_id', $filters['position_id']);
            }

            $totalUsers += (clone $query)->count();
            $registeredUsers += (clone $query)->where($table . '.has_account', 1)->count();
        }

        // --- 2. BASE QUERY USER UNTUK AKTIVITAS ---
        $userQuery = User::query()
            ->where(function ($query) {
                $query->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))->from('honda_id_verifications')
                        ->whereColumn('honda_id_verifications.honda_id', 'users.honda_id')
                        ->where('honda_id_verifications.is_active', 1)
                        ->where('honda_id_verifications.has_account', 1);
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))->from('md_id_verifications')
                        ->whereColumn('md_id_verifications.md_id', 'users.custom_id')
                        ->where('md_id_verifications.is_active', 1)
                        ->where('md_id_verifications.has_account', 1);
                })
                ->orWhereExists(function ($sub) {
                    $sub->select(DB::raw(1))->from('trainer_id_verifications')
                        ->whereColumn('trainer_id_verifications.trainer_id', 'users.trainer_id')
                        ->where('trainer_id_verifications.is_active', 1)
                        ->where('trainer_id_verifications.has_account', 1);
                });
            })
            ->when(!empty($filters['division_id']), fn($q, $v) => $q->where('division_id', $v))
            ->when(!empty($filters['position_id']), fn($q, $v) => $q->where('position_id', $v))
            ->when(!empty($filters['main_dealer_id']), fn($q, $v) => $q->where('main_dealer_id', $v))
            ->when(!empty($filters['dealer_id']), fn($q, $v) => $q->where('dealer_id', $v));

        $userIds = $userQuery->pluck('id');

        // --- 3. HITUNG AKTIVITAS & ENROLLMENT ---
        $activeUsers = DB::table('user_activities')
            ->whereIn('user_id', $userIds)
            ->where('activity_type', 'Login')
            ->whereBetween('created_at', [$start, $end])
            ->distinct('user_id')->count('user_id');

        $enrollmentQuery = DB::table('course_user')
            ->whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$start, $end])
            ->when(!empty($filters['course_id']), fn($q) => $q->where('course_id', $filters['course_id']));

        $totalEnrollment = (clone $enrollmentQuery)->count();

        $totalCompleted = (clone $enrollmentQuery)
            ->where('status', 'completed')
            ->whereBetween('completed_at', [$start, $end]) // Pastikan tgl selesai juga masuk range
            ->count();

        // --- Generate Drill-Down URLs ---
        $totalUserUrl = $this->generateTotalUserReportUrl($filters);
        $registeredUserUrl = $this->generateUserReportUrl($filters);
        $activeUserUrl = $this->generateActiveUserReportUrl($filters);
        $enrollmentReportUrlAll = $this->generateEnrollmentReportUrl($filters);
        $enrollmentReportUrlCompleted = $this->generateEnrollmentReportUrl($filters, 'completed');

        return [
            'funnelData' => [
                ['label' => 'Total Users', 'value' => $totalUsers, 'color' => '#3b82f6', 'url' => $totalUserUrl],
                ['label' => 'Registered Users', 'value' => $registeredUsers, 'color' => '#6366f1', 'url' => $registeredUserUrl],
                ['label' => 'Active Users', 'value' => $activeUsers, 'color' => '#8b5cf6', 'url' => $activeUserUrl], 
                ['label' => 'Enrolled Courses', 'value' => $totalEnrollment, 'color' => '#d946ef', 'url' => $enrollmentReportUrlAll],
                ['label' => 'Completed Courses', 'value' => $totalCompleted, 'color' => '#f43f5e', 'url' => $enrollmentReportUrlCompleted],
            ]
        ];
    }

    protected function generateTotalUserReportUrl(array $filters): string
    {
        $baseUrl = '/admin/honda-id-verifications';
        $queryParams = ['tableFilters' => ['is_active' => ['value' => '1']]];

        if (!empty($filters['main_dealer_id'])) $queryParams['tableFilters']['main_dealer_id']['value'] = $filters['main_dealer_id'];
        if (!empty($filters['dealer_id'])) $queryParams['tableFilters']['dealer_id']['value'] = $filters['dealer_id'];
        if (!empty($filters['position_id'])) $queryParams['tableFilters']['position_id']['value'] = $filters['position_id'];

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    protected function generateUserReportUrl(array $filters): string
    {
        $baseUrl = '/admin/users';
        $queryParams = [];

        if (!empty($filters['division_id'])) $queryParams['tableFilters']['division_id']['value'] = $filters['division_id'];
        if (!empty($filters['position_id'])) $queryParams['tableFilters']['position_id']['value'] = $filters['position_id'];
        if (!empty($filters['main_dealer_id'])) $queryParams['tableFilters']['main_dealer_id']['value'] = $filters['main_dealer_id'];
        if (!empty($filters['dealer_id'])) $queryParams['tableFilters']['dealer_id']['value'] = $filters['dealer_id'];

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    protected function generateActiveUserReportUrl(array $filters): string
    {
        $baseUrl = '/admin/user-activities';
        $queryParams = ['tableFilters' => ['activity_type' => ['value' => 'Login']]];

        if (!empty($filters['date_start'])) $queryParams['tableFilters']['created_at']['created_from'] = $filters['date_start'];
        if (!empty($filters['date_end'])) $queryParams['tableFilters']['created_at']['created_until'] = $filters['date_end'];
        if (!empty($filters['main_dealer_id'])) $queryParams['tableFilters']['main_dealer_id']['value'] = $filters['main_dealer_id'];

        return $baseUrl . '?' . http_build_query($queryParams);
    }

    protected function generateEnrollmentReportUrl(array $filters, ?string $status = null): string
    {
        $baseUrl = '/admin/course-users';
        $queryParams = ['tableFilters' => ['user_active' => ['value' => '1']]];
        
        if ($status) $queryParams['tableFilters']['status']['value'] = $status;

        $queryParams['tableFilters']['enrollment_date']['created_from'] = $filters['date_start'] ?? '2026-01-01';
        $queryParams['tableFilters']['enrollment_date']['created_until'] = $filters['date_end'] ?? now()->format('Y-m-d');
        
        if (!empty($filters['course_id'])) $queryParams['tableFilters']['course_id']['value'] = $filters['course_id'];
        if (!empty($filters['division_id'])) $queryParams['tableFilters']['division_id']['value'] = $filters['division_id'];
        if (!empty($filters['position_id'])) $queryParams['tableFilters']['position_id']['value'] = $filters['position_id'];
        if (!empty($filters['main_dealer_id'])) $queryParams['tableFilters']['main_dealer_id']['value'] = $filters['main_dealer_id'];
        if (!empty($filters['dealer_id'])) $queryParams['tableFilters']['dealer_id']['value'] = $filters['dealer_id'];

        return $baseUrl . '?' . http_build_query($queryParams);
    }
}