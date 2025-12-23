<?php

namespace App\Livewire;

use App\Models\OperatingExpense;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class OperatingExpensesStats extends StatsOverviewWidget
{
    protected int | array | null $columns = 1;

    public ?string $scope = null;

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Superadmin',
            'Staf Kornas',
            'Direktur Kornas',
        ]);
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $panelId = \Filament\Facades\Filament::getCurrentPanel()->getId();
        $formatIdr = fn (int|float $value) => 'Rp '.number_format($value, 0, ',', '.');

        // 1. Explicit Scope Handling (Priority)
        if ($this->scope === 'central') {
            $opsAmount = OperatingExpense::whereNull('sppg_id')->sum('amount');
            return [
                Stat::make('Biaya Ops. Kornas', $formatIdr($opsAmount))
                    ->icon('heroicon-o-banknotes', IconPosition::Before)
                    ->color('secondary')
                    ->description('Biaya Operasional Pusat'),
            ];
        }

        if ($this->scope === 'unit') {
            $opsAmount = OperatingExpense::whereNotNull('sppg_id')->sum('amount');
            return [
                Stat::make('Total Biaya Ops. SPPG', $formatIdr($opsAmount))
                    ->icon('heroicon-o-banknotes', IconPosition::Before)
                    ->color('info')
                    ->description('Akumulasi Biaya Operasional Seluruh SPPG'),
            ];
        }

        // 2. Legacy/SPPG Panel Logic (Fallback)
        if ($panelId === 'sppg') {
            $sppg = $user->hasRole('Kepala SPPG') ? $user->sppgDikepalai : $user->unitTugas->first();
            if (!$sppg) return [];

            $opsAmount = $sppg->operatingExpenses()->sum('amount');
            $rentAmount = $sppg->bills()->where('type', 'sewa_lokal')->where('status', 'paid')->sum('amount');

            return [
                Stat::make('Biaya Operasional', $formatIdr($opsAmount))
                    ->icon('heroicon-o-banknotes', IconPosition::Before)
                    ->color('secondary'),
                Stat::make('Biaya Sewa', $formatIdr($rentAmount))
                    ->icon('heroicon-o-banknotes', IconPosition::Before)
                    ->color('secondary'),
                Stat::make('Total Biaya', $formatIdr($opsAmount + $rentAmount))
                    ->icon('heroicon-o-calculator', IconPosition::Before)
                    ->color('primary'),
                Stat::make('Dana SPPG', $formatIdr($sppg->balance ?? 0))
                    ->icon('heroicon-o-currency-dollar', IconPosition::Before)
                    ->color('success'),
            ];
        }

        return [];
    }
}
