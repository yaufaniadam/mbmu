<?php

namespace App\Filament\Lembaga\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LembagaStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();
        $lembaga = $user->lembagaDipimpin;

        if (!$lembaga) {
            return [
                Stat::make('Total SPPG', 0)
                    ->description('SPPG yang dibawahi')
                    ->descriptionIcon('heroicon-m-building-office-2')
                    ->color('success'),
                Stat::make('Pending Verifikasi', 0)
                    ->description('Insentif menunggu verifikasi')
                    ->descriptionIcon('heroicon-m-clock')
                    ->color('warning'),
                Stat::make('Tagihan Belum Dibayar', 0)
                    ->description('Kontribusi Kornas')
                    ->descriptionIcon('heroicon-m-banknotes')
                    ->color('danger'),
            ];
        }

        // Count SPPG dibawahi
        $totalSppg = $lembaga->sppgs()->count();

        // Count pending verifications
        $pendingVerifications = \App\Models\Invoice::query()
            ->whereIn('sppg_id', $lembaga->sppgs->pluck('id'))
            ->where('type', 'SPPG_SEWA')
            ->where('status', 'WAITING_VERIFICATION')
            ->count();

        // Count unpaid royalty invoices
        $unpaidInvoices = \App\Models\Invoice::query()
            ->whereIn('sppg_id', $lembaga->sppgs->pluck('id'))
            ->where('type', 'LP_ROYALTY')
            ->where('status', 'UNPAID')
            ->count();

        // Total pending amount
        $totalPendingAmount = \App\Models\Invoice::query()
            ->whereIn('sppg_id', $lembaga->sppgs->pluck('id'))
            ->where('type', 'LP_ROYALTY')
            ->where('status', 'UNPAID')
            ->sum('amount');

        // Count schools for SPPGs under the Lembaga
        $sppgIds = $lembaga->sppgs->pluck('id');
        $totalSchools = \App\Models\School::whereIn('sppg_id', $sppgIds)->count();

        return [
            Stat::make('Total SPPG', $totalSppg)
                ->description('SPPG yang dibawahi')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),
            
            Stat::make('Penerima MBM', number_format($totalSchools, 0, ',', '.'))
                ->description('Total Penerima Manfaat')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('success'),
            
            Stat::make('Pending Verifikasi', $pendingVerifications)
                ->description('Insentif menunggu verifikasi')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url($pendingVerifications > 0 ? route('filament.lembaga.resources.invoices.index', ['tableFilters' => ['type' => ['value' => 'SPPG_SEWA']], 'tableFilters[status][value]' => 'WAITING_VERIFICATION']) : null),
            
            Stat::make('Tagihan Belum Dibayar', $unpaidInvoices)
                ->description('Total: Rp ' . number_format($totalPendingAmount, 0, ',', '.'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('danger')
                ->url($unpaidInvoices > 0 ? route('filament.lembaga.resources.invoices.index', ['tableFilters' => ['type' => ['value' => 'LP_ROYALTY']], 'tableFilters[status][value]' => 'UNPAID']) : null),
        ];
    }
}
