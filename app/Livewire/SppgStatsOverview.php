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
        $user = Auth::user();
        $sppg = null;
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppg = User::find($user->id)->unitTugas->first();
        } else {
            // if ($this->pageFilters['sppg_id']) {
            $sppg = Sppg::find($this->pageFilters['sppg_id']);
            // } else {
            //     $sppg = User::find($user->id)->lembagaDipimpin->sppgs->first();
            // }
        }

        if ($sppg) {
            $distributions = $sppg->distributions()->count();
            $productions = $sppg->productionSchedules()->count();
        }

        return [
            Stat::make('SPPG', $sppg->nama_sppg ?? 'N/A')
                ->icon('heroicon-o-home', IconPosition::Before)
                ->description($sppg->kepalaSppg->name ?? 'Tidak ada kepala SPPG')
                ->color('secondary'),
            Stat::make('Pengantaran', $distributions ?? 0)
                ->icon('heroicon-o-truck', IconPosition::Before)
                ->description('pengantaran selesai')
                ->color('secondary'),
            Stat::make('Produksi', $productions ?? 0)
                ->icon('heroicon-o-home-modern', IconPosition::Before)
                ->description('produksi selesai')
                ->color('secondary'),
        ];
    }
}
