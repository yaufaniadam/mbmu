<?php

namespace App\Livewire;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SppgFunds extends StatsOverviewWidget
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
            'Staf Akuntan Kornas',
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
            $incomeKornas = \App\Models\SppgIncomingFund::whereNull('sppg_id')->sum('amount');
            $expenseKornas = \App\Models\OperatingExpense::whereNull('sppg_id')->sum('amount');
            $balanceKornas = $incomeKornas - $expenseKornas;

            return [
                Stat::make('Dana Kas Kornas', $formatIdr($balanceKornas))
                    ->description('Saldo Kas Koordinator Nasional')
                    ->icon('heroicon-o-building-office-2')
                    ->color('primary'),
            ];
        }

        if ($this->scope === 'unit') {
            $totalSppgBalance = \App\Models\Sppg::sum('balance');

            return [
                Stat::make('Total Kas SPPG', $formatIdr($totalSppgBalance))
                    ->description('Akumulasi Saldo Seluruh Unit SPPG')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('info'),
            ];
        }

        // 2. Legacy/SPPG Panel Logic (Fallback)
        if ($panelId === 'sppg') {
            $sppg = $user->hasRole('Kepala SPPG') ? $user->sppgDikepalai : $user->unitTugas->first();
            $balance = $sppg?->balance ?? 0;

            return [
                Stat::make('Dana SPPG', $formatIdr($balance))
                    ->description('Saldo Dana SPPG')
                    ->icon('heroicon-o-currency-dollar', IconPosition::Before)
                    ->color('success'),
            ];
        }

        return [];
    }
}
