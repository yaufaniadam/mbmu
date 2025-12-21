<?php

namespace App\Filament\Sppg\Widgets;

use App\Models\ProductionSchedule;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class IncompletePlanAlert extends StatsOverviewWidget
{
    // Ensure widget spans full width
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        // Determine SPPG ID based on role
        $sppgId = null;
        if ($user->hasRole('Kepala SPPG')) {
             $sppg = User::find($user->id)->sppgDikepalai;
             $sppgId = $sppg?->id;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Staf Administrator SPPG', 'Ahli Gizi', 'Staf Gizi', 'Staf Akuntan', 'Staf Pengantaran'])) {
             $unitTugas = User::find($user->id)->unitTugas->first();
             $sppgId = $unitTugas?->id;
        }

        if (!$sppgId) {
            return [];
        }

        $pendingCount = ProductionSchedule::where('sppg_id', $sppgId)
            ->where('status', 'Direncanakan')
            ->whereDate('tanggal', '<=', Carbon::today())
            ->count();

        if ($pendingCount === 0) {
            return [];
        }

        return [
            Stat::make('Perhatian', "$pendingCount Rencana Distribusi")
                ->description('Status masih "Direncanakan". Segera lengkapi menu & verifikasi.')
                ->descriptionIcon('heroicon-s-exclamation-triangle')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'cursor-pointer',
                    'onclick' => "window.location.href = '/sppg/production-schedules'",
                ]),
        ];
    }
}
