<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsStats;
use App\Filament\Widgets\VisitorsChart;
use Filament\Pages\Page;

class LaporanAnalytics extends Page
{
    // Icon yang akan muncul di sidebar
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    // Nama menu di sidebar
    protected static ?string $navigationLabel = 'Statistik Pengunjung';

    // Mengelompokkan ke dalam grup "Laporan"
    protected static ?string $navigationGroup = 'Laporan';

    // File view blade yang digunakan
    protected static string $view = 'filament.pages.laporan-analytics';

    /**
     * Mendaftarkan widget agar tampil di halaman ini.
     * Kita letakkan di Header agar muncul di bagian atas content.
     */
    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsStats::class,
            VisitorsChart::class,
        ];
    }

    /**
     * Mengatur jumlah kolom widget (opsional)
     * Full width untuk chart, dan grid untuk stats
     */
    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }
}