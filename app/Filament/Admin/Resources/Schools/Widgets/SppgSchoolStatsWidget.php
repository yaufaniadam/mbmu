<?php

namespace App\Filament\Admin\Resources\Schools\Widgets;

use App\Models\Sppg;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SppgSchoolStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $allSppgs = Sppg::withCount('schools')->get();
        $totalSppg = $allSppgs->count();
        
        $filledSppg = $allSppgs->filter(fn($sppg) => $sppg->schools_count > 0)->count();
        $unfilledSppg = $totalSppg - $filledSppg;

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('Total pendaftaran unit SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Sudah Input Sekolah', $filledSppg)
                ->description('Unit yang sudah mendaftarkan sekolah')
                ->color('success')
                ->icon('heroicon-m-academic-cap'),
            Stat::make('Belum Input Sekolah', $unfilledSppg)
                ->description('Unit yang belum mendaftarkan sekolah')
                ->color('danger')
                ->icon('heroicon-m-x-circle'),
        ];
    }
}
