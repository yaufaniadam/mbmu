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
        $totalCount = Sppg::count();
        $operasionalCount = Sppg::where('status', 'Operasional / Siap Berjalan')->count();
        $verifikasiCount = Sppg::where('status', 'Verifikasi dan Validasi')->count();
        $persiapanCount = Sppg::where('status', 'Proses Persiapan')->count();

        return [
            Stat::make('Total SPPG', $totalCount)
                ->icon('heroicon-m-building-office-2'),
            Stat::make('Operasional', $operasionalCount)
                ->color('success')
                ->icon('heroicon-m-check-circle'),
            Stat::make('Verifikasi', $verifikasiCount)
                ->color('info')
                ->icon('heroicon-m-magnifying-glass'),
            Stat::make('Persiapan', $persiapanCount)
                ->color('warning')
                ->icon('heroicon-m-clock'),
        ];
    }
}
