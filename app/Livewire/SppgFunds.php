<?php

namespace App\Livewire;

use Filament\Support\Enums\IconPosition;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class SppgFunds extends StatsOverviewWidget
{
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
        $isNational = \Filament\Facades\Filament::getCurrentPanel()->getId() === 'admin';
        
        $balance = 0;
        $label = 'Dana SPPG';
        $desc = 'Saldo Dana SPPG';

        if ($isNational) {
            // Calculate National Balance (Always Kornas Kas)
            $income = \App\Models\SppgIncomingFund::whereNull('sppg_id')->sum('amount');
            $expense = \App\Models\OperatingExpense::whereNull('sppg_id')->sum('amount');
            $balance = $income - $expense;
            
            $label = 'Dana Kas Kornas';
            $desc = 'Saldo Kas Kantor Nasional';
        } else {
            // SPPG Panel Logic: Always show specific SPPG balance
            $sppg = $user->hasRole('Kepala SPPG')
                ? $user->sppgDikepalai
                : $user->unitTugas->first();
            $balance = $sppg?->balance ?? 0;
        }

        $formatIdr = fn (int|float $value) => 'Rp '.number_format($value, 0, ',', '.');

        return [
            Stat::make($label, $formatIdr($balance))
                ->description($desc)
                ->icon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),
        ];
    }
}
