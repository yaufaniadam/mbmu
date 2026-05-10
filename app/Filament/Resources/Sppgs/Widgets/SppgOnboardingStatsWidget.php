<?php

namespace App\Filament\Resources\Sppgs\Widgets;

use App\Models\Sppg;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SppgOnboardingStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $allSppgs = Sppg::all();
        $totalCount = $allSppgs->count();
        
        $completeCount = $allSppgs->filter(fn($sppg) => $sppg->completion_score >= 100)->count();
        $incompleteCount = $totalCount - $completeCount;

        return [
            Stat::make('Total SPPG', $totalCount)
                ->description('Total pendaftaran SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Profil Lengkap', $completeCount)
                ->description('Sudah melengkapi 100% data')
                ->color('success')
                ->icon('heroicon-m-check-badge'),
            Stat::make('Profil Belum Lengkap', $incompleteCount)
                ->description('Masih perlu melengkapi data')
                ->color('warning')
                ->icon('heroicon-m-exclamation-triangle'),
        ];
    }
}
