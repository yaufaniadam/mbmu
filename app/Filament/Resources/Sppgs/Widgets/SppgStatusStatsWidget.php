<?php

namespace App\Filament\Resources\Sppgs\Widgets;

use App\Models\Sppg;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SppgStatusStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $operasionalCount = Sppg::where('status', 'Operasional / Siap Berjalan')->count();
        $verifikasiCount = Sppg::where('status', 'Verifikasi dan Validasi')->count();
        $persiapanCount = Sppg::where('status', 'Proses Persiapan')->count();

        return [
            Stat::make('Operasional', $operasionalCount)
                ->color('success')
                ->description('Siap Berjalan')
                ->icon('heroicon-m-check-circle'),
            Stat::make('Verifikasi', $verifikasiCount)
                ->color('info')
                ->description('Verval')
                ->icon('heroicon-m-magnifying-glass'),
            Stat::make('Persiapan', $persiapanCount)
                ->color('warning')
                ->description('Dalam Persiapan')
                ->icon('heroicon-m-clock'),
        ];
    }
}
