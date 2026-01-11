<?php

namespace App\Livewire;

use App\Models\ProductionSchedule;
use App\Models\User;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Facades\Auth;

class ProductionChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Aktifitas Harian';

    protected function getData(): array
    {
        $user = Auth::user();

        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = User::find($user->id)->sppgDikepalai?->id;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Ahli Gizi', 'Staf Administrator SPPG', 'Staf Akuntan', 'Staf Gizi', 'Staf Pengantaran'])) {
            $sppgId = User::find($user->id)->unitTugas->first()?->id;
        } else {
            $sppgId = $this->pageFilters['sppg_id'] ?? null;
        }

        $query = ProductionSchedule::query();
        if ($sppgId) {
            $query->where('sppg_id', $sppgId);
        }

        $data = Trend::query($query)
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            // ->where('sppg_id', $sppgId) // This was the error
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Produksi MBG Bulanan',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
