<?php

namespace App\Livewire;

use App\Models\Sppg;
use App\Models\User;
use Filament\Support\Enums\IconPosition;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

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
            $distributions = \App\Models\Distribution::count();
            $productions = \App\Models\ProductionSchedule::count();
            $sppgCount = Sppg::count();
        } else if ($sppg) {
            $distributions = $sppg->distributions()->count();
            $productions = $sppg->productionSchedules()->count();
        }

        return [
            $isNationalView
                ? Stat::make('Total SPPG Terdaftar', $sppgCount ?? 0)
                    ->icon('heroicon-o-home-modern', IconPosition::Before)
                    ->description('Seluruh Indonesia')
                    ->color('primary')
                : Stat::make('SPPG', $sppg->nama_sppg ?? 'N/A')
                    ->icon('heroicon-o-home', IconPosition::Before)
                    ->description($sppg->kepalaSppg->name ?? 'Tidak ada kepala SPPG')
                    ->color('secondary'),
            Stat::make('Pengantaran', $distributions ?? 0)
                ->icon('heroicon-o-truck', IconPosition::Before)
                ->description($isNationalView ? 'Total pengantaran nasional' : 'pengantaran selesai')
                ->color('secondary'),
            Stat::make('Produksi', $productions ?? 0)
                ->icon('heroicon-o-home-modern', IconPosition::Before)
                ->description($isNationalView ? 'Total produksi nasional' : 'produksi selesai')
                ->color('secondary'),
        ];
    }
}
