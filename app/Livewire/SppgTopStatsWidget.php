<?php

namespace App\Livewire;

use App\Models\ProductionSchedule;
use App\Models\Sppg;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class SppgTopStatsWidget extends Widget
{
    use InteractsWithPageFilters;

    protected string $view = 'livewire.sppg-top-stats-widget';

    protected int|string|array $columnSpan = 'full';


    public function getData()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) return [
            'sppg' => null,
            'isNationalView' => true,
            'pendingCount' => 0,
        ];

        $sppg = null;
        $isNationalView = false;

        // 1. Check for SPPG Head role
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = Sppg::where('kepala_sppg_id', $user->id)->first();
        } 
        
        // 2. Check for Staff Roles
        if (!$sppg && $user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $sppg = Sppg::whereHas('staff', function($query) use ($user) {
                $query->where('users.id', $user->id);
            })->first();
        }

        // 3. Fallback to filters for Management Roles
        if (!$sppg) {
            $sppgId = $this->pageFilters['sppg_id'] ?? null;
            if ($sppgId) {
                $sppg = Sppg::find($sppgId);
            } else {
                $isNationalView = true;
            }
        }

        // 4. Calculate pending count
        $pendingCount = 0;
        if ($sppg) {
            $pendingCount = ProductionSchedule::where('sppg_id', $sppg->id)
                ->where('status', 'Direncanakan')
                ->whereDate('tanggal', '<=', Carbon::today())
                ->count();
        }

        return [
            'sppg' => $sppg,
            'isNationalView' => $isNationalView,
            'pendingCount' => $pendingCount,
        ];
    }

    protected function getViewData(): array
    {
        return $this->getData();
    }
}
