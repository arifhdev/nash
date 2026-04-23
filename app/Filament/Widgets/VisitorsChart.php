<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class VisitorsChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Tren Pengunjung';
    protected static ?string $maxHeight = '300px';

    // 1. Properti filter bawaan agar saat halaman dimuat, defaultnya '7_days'
    public ?string $filter = '7_days';

    public static function canView(): bool
    {
        return str_contains(request()->url(), 'laporan-analytics');
    }

    // 2. Menambahkan Menu Filter Dropdown di Pojok Kanan Atas Chart
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hari Ini',
            '7_days' => '7 Hari Terakhir',
            '30_days' => '30 Hari Terakhir',
            'this_month' => 'Bulan Ini',
            'this_year' => 'Tahun Ini',
        ];
    }

    protected function getData(): array
    {
        // 3. Logika untuk mengubah Periode Google Analytics berdasarkan filter yang dipilih
        $period = match ($this->filter) {
            'today' => Period::days(1),
            '7_days' => Period::days(6),
            '30_days' => Period::days(29),
            'this_month' => Period::create(Carbon::now()->startOfMonth(), Carbon::now()),
            'this_year' => Period::create(Carbon::now()->startOfYear(), Carbon::now()),
            default => Period::days(6),
        };

        // 4. Custom Query GA4: Tarik 3 Metrik Sekaligus (Users, PageViews, Sessions)
        $analyticsData = Analytics::get(
            $period,
            ['activeUsers', 'screenPageViews', 'sessions'], // Metrik yang ditarik
            ['date'] // Dimensi (dikelompokkan per tanggal)
        );

        $labels = [];
        $visitors = [];
        $pageViews = [];
        $sessions = []; // Wadah untuk data ke-3

        foreach ($analyticsData as $data) {
            // Amankan pengambilan data (Bisa berupa array atau object)
            $dateRaw = is_array($data) ? ($data['date'] ?? null) : ($data->date ?? null);
            
            // GA4 kadang melempar format string 'YYYYMMDD' murni jika pakai method get()
            if ($dateRaw instanceof Carbon || $dateRaw instanceof \DateTime) {
                $labels[] = $dateRaw->translatedFormat('d M');
            } elseif (is_string($dateRaw) && strlen($dateRaw) === 8 && is_numeric($dateRaw)) {
                $labels[] = Carbon::createFromFormat('Ymd', $dateRaw)->translatedFormat('d M');
            } elseif (is_string($dateRaw)) {
                $labels[] = Carbon::parse($dateRaw)->translatedFormat('d M');
            } else {
                $labels[] = '-';
            }

            // Memasukkan metrik ke array dengan pengaman fallback '0'
            $visitors[] = is_array($data) ? ($data['activeUsers'] ?? 0) : ($data->activeUsers ?? 0);
            $pageViews[] = is_array($data) ? ($data['screenPageViews'] ?? 0) : ($data->screenPageViews ?? 0);
            $sessions[] = is_array($data) ? ($data['sessions'] ?? 0) : ($data->sessions ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pengunjung Unik',
                    'data' => $visitors,
                    'fill' => 'start',
                    'borderColor' => '#3b82f6', // Biru
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Sesi (Kunjungan)', // GARIS KE-3 BARU
                    'data' => $sessions,
                    'fill' => 'start',
                    'borderColor' => '#10b981', // Hijau Emerald
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Tayangan Halaman',
                    'data' => $pageViews,
                    'fill' => 'start',
                    'borderColor' => '#f43f5e', // Merah Muda
                    'backgroundColor' => 'rgba(244, 63, 94, 0.1)',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}