<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\Period;
use Carbon\Carbon;

class AnalyticsStats extends BaseWidget // Sesuaikan nama class dengan milik Anda
{
    /**
     * Memastikan widget ini hanya tampil di halaman LaporanAnalytics
     */
    public static function canView(): bool
    {
        return str_contains(request()->url(), 'laporan-analytics');
    }

    // 1. Tambahkan properti filter default
    public ?string $filter = '7_days';

    // 2. Tambahkan Dropdown Filter di pojok kanan atas kumpulan Stat
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

    protected function getStats(): array
    {
        // 3. Logika periode berdasarkan filter yang dipilih
        $period = match ($this->filter) {
            'today' => Period::days(1),
            '7_days' => Period::days(6),
            '30_days' => Period::days(29),
            'this_month' => Period::create(Carbon::now()->startOfMonth(), Carbon::now()),
            'this_year' => Period::create(Carbon::now()->startOfYear(), Carbon::now()),
            default => Period::days(6),
        };

        // Agar judul deskripsi di dalam card dinamis mengikuti filter
        $labelWaktu = match ($this->filter) {
            'today' => '(Hari Ini)',
            '7_days' => '(7 Hari)',
            '30_days' => '(30 Hari)',
            'this_month' => '(Bulan Ini)',
            'this_year' => '(Tahun Ini)',
            default => '(7 Hari)',
        };

        // 4. Tarik data total dari GA4 (Aman dari error date)
        $analyticsData = Analytics::get(
            $period,
            ['activeUsers', 'screenPageViews', 'sessions']
        );

        // Gunakan sum() agar aman jika Google mengembalikan format collection multi-dimensi
        $visitors = $analyticsData->sum('activeUsers') ?? 0;
        $sessions = $analyticsData->sum('sessions') ?? 0;
        $pageViews = $analyticsData->sum('screenPageViews') ?? 0;

        // 5. Kembalikan 3 Kotak (Cards)
        return [
            Stat::make("Total Pengunjung {$labelWaktu}", $visitors)
                ->description('Pengunjung unik terdeteksi')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            // INI KOTAK KE-3 (TENGAH) YANG BARU
            Stat::make("Total Sesi {$labelWaktu}", $sessions)
                ->description('Total interaksi kunjungan')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('warning'),

            Stat::make("Total Tayangan {$labelWaktu}", $pageViews)
                ->description('Halaman yang diakses')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),
        ];
    }
}