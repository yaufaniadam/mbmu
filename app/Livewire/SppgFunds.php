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
        ]);
    }

    protected function getStats(): array
    {
        $user = Auth::user();

        $sppg = $user->hasRole('Kepala SPPG')
            ? $user->sppgDikepalai
            : $user->unitTugas->first();

        $formatIdr = fn (int|float $value) => 'Rp '.number_format($value, 0, ',', '.');

        return [
            Stat::make('Dana SPPG', $formatIdr($sppg?->balance ?? 0))
                ->description('Saldo Dana SPPG')
                ->icon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success'),
        ];
    }
}
