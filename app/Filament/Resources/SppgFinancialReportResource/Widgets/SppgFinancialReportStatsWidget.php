<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Widgets;

use App\Models\Sppg;
use App\Models\SppgFinancialReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class SppgFinancialReportStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSppg = Sppg::count();

        // SPPG yang sudah lapor keuangan
        $hasFinance = SppgFinancialReport::where('category', 'keuangan')->distinct('sppg_id')->pluck('sppg_id')->toArray();
        
        // SPPG yang sudah lapor kegiatan
        $hasActivity = SppgFinancialReport::where('category', 'kegiatan')->distinct('sppg_id')->pluck('sppg_id')->toArray();

        $bothCount = count(array_intersect($hasFinance, $hasActivity));
        $noneCount = $totalSppg - count(array_unique(array_merge($hasFinance, $hasActivity)));
        $incompleteCount = $totalSppg - $bothCount - $noneCount;

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('Total unit SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Lengkap (Keuangan & Kegiatan)', $bothCount)
                ->description('Unit yang sudah upload kedua laporan')
                ->color('success')
                ->icon('heroicon-m-check-badge'),
            Stat::make('Belum Lengkap / Belum Lapor', ($totalSppg - $bothCount))
                ->description('Unit yang masih ada tunggakan laporan')
                ->color('danger')
                ->icon('heroicon-m-exclamation-circle'),
        ];
    }
}
