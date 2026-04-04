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
    protected static bool $isLazy = false;


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

        $managedSppg = User::find($user->id)->getManagedSppg();

        if ($managedSppg) {
            $sppg = $managedSppg;
        } else {
            $sppgId = ($this->pageFilters ?? [])['sppg_id'] ?? null;
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
