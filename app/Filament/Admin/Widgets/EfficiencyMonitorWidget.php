<?php

namespace App\Filament\Admin\Widgets;

use App\Models\OperatingExpense;
use App\Models\ProductionSchedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class EfficiencyMonitorWidget extends BaseWidget
{
    public static function canView(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas']);
    }

    protected function getStats(): array
    {
        $totalExpense = OperatingExpense::sum('amount');
        $totalPortions = ProductionSchedule::sum('jumlah');

        $costPerPortion = $totalPortions > 0 ? $totalExpense / $totalPortions : 0;

        return [
            Stat::make('Rata-rata Biaya per Porsi (Nasional)', 'Rp ' . number_format($costPerPortion, 0, ',', '.'))
                ->description('Total Belanja / Total Porsi')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Total Porsi Tersalurkan', number_format($totalPortions, 0, ',', '.'))
                ->description('Seluruh SPPG Nasional')
                ->descriptionIcon('heroicon-m-shopping-cart'),
            Stat::make('Total Anggaran Terpakai', 'Rp ' . number_format($totalExpense, 0, ',', '.'))
                ->description('Seluruh SPPG Nasional')
                ->descriptionIcon('heroicon-m-credit-card')
                ->color('info'),
        ];
    }
}
