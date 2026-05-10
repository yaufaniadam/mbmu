<?php

namespace App\Filament\Admin\Resources\Volunteers\Widgets;

use App\Models\Sppg;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SppgVolunteerStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $allSppgs = Sppg::withCount('volunteers')->get();
        $totalSppg = $allSppgs->count();
        
        $filledSppg = $allSppgs->filter(fn($sppg) => $sppg->volunteers_count > 0)->count();
        $unfilledSppg = $totalSppg - $filledSppg;

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('Total pendaftaran unit SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Sudah Input Relawan', $filledSppg)
                ->description('Unit yang sudah mendaftarkan relawan')
                ->color('success')
                ->icon('heroicon-m-user-plus'),
            Stat::make('Belum Input Relawan', $unfilledSppg)
                ->description('Unit yang belum mendaftarkan relawan')
                ->color('danger')
                ->icon('heroicon-m-user-minus'),
        ];
    }
}
