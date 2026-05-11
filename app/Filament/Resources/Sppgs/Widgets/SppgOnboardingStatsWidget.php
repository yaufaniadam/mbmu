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
        
        $noStaffCount = Sppg::whereDoesntHave('staffs')->count();

        return [
            Stat::make('Total SPPG', $totalCount)
                ->description('Total pendaftaran SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Profil Belum Lengkap', $incompleteCount)
                ->description('Unit yang belum 100% mengisi data')
                ->color('warning')
                ->icon('heroicon-m-exclamation-triangle'),
            Stat::make('Belum Isi Anggota', $noStaffCount)
                ->description('Unit yang belum mendaftarkan staf')
                ->color('danger')
                ->icon('heroicon-m-user-group'),
        ];
    }
}
