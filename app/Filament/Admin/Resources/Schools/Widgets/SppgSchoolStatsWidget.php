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

        $totalSchools = \App\Models\School::count();
        $totalPorsiBesar = \App\Models\School::sum('default_porsi_besar');
        $totalPorsiKecil = \App\Models\School::sum('default_porsi_kecil');
        $grandTotal = $totalPorsiBesar + $totalPorsiKecil;

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('Total unit SPPG')
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Belum Input Penerima', $unfilledSppg)
                ->description('Unit yang belum daftar sekolah')
                ->color('danger')
                ->icon('heroicon-m-x-circle'),
            Stat::make('Total Lembaga Penerima', $totalSchools)
                ->description('Sekolah/Unit yang terdaftar')
                ->color('success')
                ->icon('heroicon-m-academic-cap'),
            Stat::make('Total Penerima (Porsi)', number_format($grandTotal))
                ->description($totalPorsiBesar . ' Besar, ' . $totalPorsiKecil . ' Kecil')
                ->color('info')
                ->icon('heroicon-m-users'),
        ];
    }
}
