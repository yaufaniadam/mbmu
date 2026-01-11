<?php

namespace App\Livewire;

use App\Models\Sppg;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SppgStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $sppg = null;
        $isNationalView = false;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $sppg = User::find($user->id)->unitTugas->first();
        } else {
            $sppgId = $this->pageFilters['sppg_id'] ?? null;
            if ($sppgId) {
                $sppg = Sppg::find($sppgId);
            } else {
                $isNationalView = true;
            }
        }

        if ($isNationalView) {
            $totalSppg = Sppg::count();
            // Count staff (Users attached to SPPGs via pivoting or specific roles if needed, but simplest is all users with staff roles? 
            // Better to count distinct users in sppg_user_roles pivot if possible, or just users who have any staff role?)
            // Let's assume for now we count all users who have roles associated with SPPG operations.
            // Actually, querying the pivot table from Sppg model context is safer if we want "Staff at SPPGs".
            // But for simplicity/performance in this widget:
            $totalStaf = DB::table('sppg_user_roles')->distinct('user_id')->count(); 
            $totalRelawan = \App\Models\Volunteer::count();

            return [
                Stat::make('Total SPPG', number_format($totalSppg, 0, ',', '.'))
                    ->icon('heroicon-o-building-storefront', IconPosition::Before)
                    ->description('Total unit SPPG nasional')
                    ->color('primary'),
                Stat::make('Total Staf', number_format($totalStaf, 0, ',', '.'))
                    ->icon('heroicon-o-users', IconPosition::Before)
                    ->description('Total staf terdaftar')
                    ->color('success'),
                Stat::make('Total Relawan', number_format($totalRelawan, 0, ',', '.'))
                    ->icon('heroicon-o-user-group', IconPosition::Before)
                    ->description('Total relawan aktif')
                    ->color('warning'),
            ];
        } else if ($sppg) {
            $totalStaf = $sppg->staff()->count();
            $totalRelawan = $sppg->volunteers()->count();

            return [
                Stat::make('Total SPPG', '1')
                    ->icon('heroicon-o-building-storefront', IconPosition::Before)
                    ->description('Unit Operasional')
                    ->color('primary'),
                Stat::make('Total Staf', number_format($totalStaf, 0, ',', '.'))
                    ->icon('heroicon-o-users', IconPosition::Before)
                    ->description('Staf unit ini')
                    ->color('success'),
                Stat::make('Total Relawan', number_format($totalRelawan, 0, ',', '.'))
                    ->icon('heroicon-o-user-group', IconPosition::Before)
                    ->description('Relawan unit ini')
                    ->color('warning'),
            ];
        }

        return [];
    }
}
