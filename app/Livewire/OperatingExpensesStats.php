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

        // 1. Initialize variables
        $opsAmount = 0;
        $rentAmount = 0;
        $showRentAndTotal = false; // By default, only show Ops (matches your admin logic)

        // 2. Logic for Local Roles (Kepala SPPG / PJ Pelaksana)
        if ($user->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana'])) {
            $showRentAndTotal = true;

            // Determine SPPG based on role
            $sppg = $user->hasRole('Kepala SPPG')
                ? $user->sppgDikepalai
                : $user->unitTugas->first();

            if ($sppg) {
                $opsAmount = $sppg->operatingExpenses()->sum('amount');

                $rentAmount = $sppg->bills()
                    ->where('type', 'sewa_lokal')
                    ->where('status', 'paid')
                    ->sum('amount');
            }
        }
        // 3. Logic for Admin Roles
        elseif ($user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Direktur Kornas'])) {
            $opsAmount = OperatingExpense::whereNull('sppg_id')->sum('amount');
        } else {
            // If user has no relevant role, return empty
            return [];
        }

        // 4. Helper for IDR Formatting
        // e.g., converts 5000000 to "Rp 5.000.000"
        $formatIdr = fn (int|float $value) => 'Rp '.number_format($value, 0, ',', '.');

        // 5. Build the Stats Array
        $stats = [
            Stat::make('Biaya Operasional', $formatIdr($opsAmount))
                ->icon('heroicon-o-banknotes', IconPosition::Before)
                ->color('secondary')
                ->columnSpan(2),
        ];

        // 6. Conditionally add Rent and Total stats
        if ($showRentAndTotal) {
            $stats[] = Stat::make('Biaya Sewa', $formatIdr($rentAmount))
                ->icon('heroicon-o-banknotes', IconPosition::Before)
                ->color('secondary')
                ->columnSpan(2);

            $stats[] = Stat::make('Total Biaya', $formatIdr($opsAmount + $rentAmount))
                ->icon('heroicon-o-calculator', IconPosition::Before)
                ->color('primary')
                ->columnSpan(2);

            $stats[] = Stat::make('Dana SPPG', $formatIdr($sppg?->balance ?? 0))
                ->icon('heroicon-o-currency-dollar', IconPosition::Before)
                ->color('success')
                ->columnSpan(2);
        }

        return $stats;
    }
}
