<?php

namespace App\Livewire;

use App\Models\Invoice;
use App\Models\OperatingExpense;
use App\Models\Sppg;
use App\Models\SppgIncomingFund;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class FinanceStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sppg = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $sppg = User::find($user->id)->unitTugas->first();
        } else {
            $sppgId = $this->pageFilters['sppg_id'] ?? null;
            if ($sppgId) {
                $sppg = Sppg::find($sppgId);
            }
        }

        if ($sppg) {
            // 1. Total Income (Incoming Funds)
            $totalIncome = SppgIncomingFund::where('sppg_id', $sppg->id)
                ->sum('amount');

            // 2. Total Expenses (Operating Expenses + Paid Rent Invoices)
            $opExpenses = OperatingExpense::where('sppg_id', $sppg->id)
                ->sum('amount');
                
            $rentPaid = Invoice::where('sppg_id', $sppg->id)
                ->where('type', 'SPPG_SEWA')
                ->where('status', 'PAID')
                ->sum('amount');
                
            $totalExpenses = $opExpenses + $rentPaid;
            $description = 'Data unit ' . $sppg->nama_sppg;
        } else {
            // National aggregated view
            $totalIncome = SppgIncomingFund::sum('amount');
            $opExpenses = OperatingExpense::sum('amount');
            $rentPaid = Invoice::where('type', 'SPPG_SEWA')
                ->where('status', 'PAID')
                ->sum('amount');
            $totalExpenses = $opExpenses + $rentPaid;
            $description = 'Agregat Nasional';
        }

        // 3. Balance
        $balance = $totalIncome - $totalExpenses;

        return [
            Stat::make('Total Pemasukan', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-up', IconPosition::Before)
                ->description($description)
                ->color('success'),
            Stat::make('Total Pengeluaran', 'Rp ' . number_format($totalExpenses, 0, ',', '.'))
                ->icon('heroicon-o-arrow-trending-down', IconPosition::Before)
                ->description('Operasional + Sewa Lunas')
                ->color('danger'),
            Stat::make('Saldo Bersih', 'Rp ' . number_format($balance, 0, ',', '.'))
                ->icon('heroicon-o-calculator', IconPosition::Before)
                ->description($description)
                ->color($balance >= 0 ? 'success' : 'danger'),
        ];
    }
}
