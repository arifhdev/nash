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
use Carbon\CarbonPeriod;

class EnrollmentTrendChartWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.enrollment-trend-chart-widget';
    protected int | string | array $columnSpan = 'full';
    public ?array $filterData = [];

    public function mount(): void
    {
        // Default: 1 Bulan Terakhir s/d Hari Ini
        $this->form->fill([
            'date_start' => now()->subMonth()->format('Y-m-d'),
            'date_end' => now()->format('Y-m-d'),
        ]);
    }

    // Hook otomatis saat filter berubah
    public function updatedFilterData()
    {
        $data = $this->calculateChartData();
        
        // Dispatch data ke JavaScript
        $this->dispatch('updateChart', [
            'active' => $data['active'],
            'enrolled' => $data['enrolled'],
            'completed' => $data['completed'],
            'labels' => $data['labels'],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(7)->schema([
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

    protected function calculateChartData(): array
    {
        $filters = $this->filterData;
        $startDateStr = $filters['date_start'] ?? now()->subMonth()->format('Y-m-d');
        $endDateStr = $filters['date_end'] ?? now()->format('Y-m-d');
        
        $start = $startDateStr . ' 00:00:00';
        $end = $endDateStr . ' 23:59:59';

        // 1. Ambil User Whitelist yang valid
        $userQuery = User::query()
            ->where(function ($query) {
                $query->whereExists(fn($sub) => $sub->select(DB::raw(1))->from('honda_id_verifications')->whereColumn('honda_id_verifications.honda_id', 'users.honda_id')->where('is_active', 1))
                ->orWhereExists(fn($sub) => $sub->select(DB::raw(1))->from('md_id_verifications')->whereColumn('md_id_verifications.md_id', 'users.custom_id')->where('is_active', 1))
                ->orWhereExists(fn($sub) => $sub->select(DB::raw(1))->from('trainer_id_verifications')->whereColumn('trainer_id_verifications.trainer_id', 'users.trainer_id')->where('is_active', 1));
            })
            ->when(!empty($filters['division_id']), fn($q, $v) => $q->where('division_id', $v))
            ->when(!empty($filters['position_id']), fn($q, $v) => $q->where('position_id', $v))
            ->when(!empty($filters['main_dealer_id']), fn($q, $v) => $q->where('main_dealer_id', $v))
            ->when(!empty($filters['dealer_id']), fn($q, $v) => $q->where('dealer_id', $v));

        if (!empty($filters['course_id'])) {
            $userQuery->whereHas('courses', fn($q) => $q->where('course_id', $filters['course_id']));
        }

        $userIds = $userQuery->pluck('id');

        // 2. Mapping Periode Harian
        $period = CarbonPeriod::create($startDateStr, $endDateStr);
        $labels = []; $active = []; $enrolled = []; $completed = [];

        foreach ($period as $date) {
            $key = $date->format('Y-m-d');
            $labels[] = $date->format('d M');
            $active[$key] = 0; $enrolled[$key] = 0; $completed[$key] = 0;
        }

        if ($userIds->isNotEmpty()) {
            // Logins
            $resLogin = DB::table('user_activities')->select(DB::raw('DATE(created_at) as tgl'), DB::raw('COUNT(DISTINCT user_id) as c'))
                ->whereIn('user_id', $userIds)->where('activity_type', 'Login')->whereBetween('created_at', [$start, $end])->groupBy('tgl')->pluck('c', 'tgl');
            foreach ($resLogin as $tgl => $c) { if(isset($active[$tgl])) $active[$tgl] = $c; }

            // Enrollments
            $resEnroll = DB::table('course_user')->select(DB::raw('DATE(created_at) as tgl'), DB::raw('COUNT(*) as c'))
                ->whereIn('user_id', $userIds)->whereBetween('created_at', [$start, $end])
                ->when(!empty($filters['course_id']), fn($q) => $q->where('course_id', $filters['course_id']))
                ->groupBy('tgl')->pluck('c', 'tgl');
            foreach ($resEnroll as $tgl => $c) { if(isset($enrolled[$tgl])) $enrolled[$tgl] = $c; }

            // Completed
            $resComplete = DB::table('course_user')->select(DB::raw('DATE(completed_at) as tgl'), DB::raw('COUNT(*) as c'))
                ->whereIn('user_id', $userIds)->where('status', 'completed')->whereBetween('completed_at', [$start, $end])
                ->when(!empty($filters['course_id']), fn($q) => $q->where('course_id', $filters['course_id']))
                ->groupBy('tgl')->pluck('c', 'tgl');
            foreach ($resComplete as $tgl => $c) { if(isset($completed[$tgl])) $completed[$tgl] = $c; }
        }

        return [
            'labels' => $labels,
            'active' => array_values($active),
            'enrolled' => array_values($enrolled),
            'completed' => array_values($completed),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'chartData' => $this->calculateChartData()
        ];
    }
}