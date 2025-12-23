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
            $distributions = \App\Models\Distribution::where('status_pengantaran', 'Terkirim')->count();
            $productions = \App\Models\ProductionSchedule::where('status', 'Selesai')->count();
            $sppgCount = Sppg::count();
            // Hanya hitung yang sudah terkirim tapi belum dijemput
            $pendingPickups = \App\Models\Distribution::where('status_pengantaran', 'Terkirim')
                ->where(function($query) {
                    $query->where('pickup_status', '!=', 'Dijemput')
                          ->orWhereNull('pickup_status');
                })
                ->count();
        } else if ($sppg) {
            // Hanya hitung untuk SPPG bersangkutan dengan status selesai
            $distributions = $sppg->distributions()->where('status_pengantaran', 'Terkirim')->count();
            $productions = $sppg->productionSchedules()->where('status', 'Selesai')->count();
            // Hanya hitung yang sudah terkirim tapi belum dijemput
            $pendingPickups = $sppg->distributions()
                ->where('status_pengantaran', 'Terkirim')
                ->where(function($query) {
                    $query->where('pickup_status', '!=', 'Dijemput')
                          ->orWhereNull('pickup_status');
                })
                ->count();
        }

        return [
            Stat::make('Pengantaran', $distributions ?? 0)
                ->icon('heroicon-o-truck', IconPosition::Before)
                ->description($isNationalView ? 'Total pengantaran nasional' : 'pengantaran selesai')
                ->color('secondary'),
            Stat::make('Produksi', $productions ?? 0)
                ->icon('heroicon-o-home-modern', IconPosition::Before)
                ->description($isNationalView ? 'Total produksi nasional' : 'produksi selesai')
                ->color('secondary'),
            Stat::make('Penjemputan Alat', $pendingPickups ?? 0)
                ->icon('heroicon-o-arrow-path', IconPosition::Before)
                ->description('Peralatan belum dijemput')
                ->color(($pendingPickups ?? 0) > 0 ? 'warning' : 'success'),
        ];
    }
}
