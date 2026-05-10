<?php

namespace App\Filament\Resources\ProductionSchedules\Widgets;

use App\Models\Sppg;
use App\Models\ProductionSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SppgProductionScheduleStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $totalSppg = Sppg::count();
        
        // SPPG yang sudah punya minimal 1 jadwal
        $filledSppgCount = ProductionSchedule::distinct('sppg_id')->count('sppg_id');
        $unfilledSppgCount = $totalSppg - $filledSppgCount;

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('Total unit SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Sudah Buat Jadwal', $filledSppgCount)
                ->description('Unit yang sudah input jadwal produksi')
                ->color('success')
                ->icon('heroicon-m-calendar-days'),
            Stat::make('Belum Buat Jadwal', $unfilledSppgCount)
                ->description('Unit yang belum memiliki jadwal')
                ->color('danger')
                ->icon('heroicon-m-calendar-date-range'),
        ];
    }
}
