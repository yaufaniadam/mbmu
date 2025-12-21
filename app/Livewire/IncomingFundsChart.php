<?php

namespace App\Livewire;

use App\Models\SppgIncomingFund;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomingFundsChart extends ChartWidget
{
    protected ?string $heading = 'Dana Masuk - Grafik';

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Superadmin',
            'Staf Kornas',
            'Staf Akuntan Kornas',
            'Direktur Kornas',
        ]);
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $panelId = \Filament\Facades\Filament::getCurrentPanel()->getId();

        // Determine scope based on panel
        $query = SppgIncomingFund::query()
            ->whereNotNull('received_at');

        if ($panelId === 'admin') {
            // Admin panel: National data (sppg_id = null)
            $query->whereNull('sppg_id');
        } elseif ($panelId === 'sppg') {
            // SPPG panel: Scope to user's SPPG
            $sppgId = $user->hasRole('Kepala SPPG')
                ? $user->sppgDikepalai?->id
                : $user->unitTugas->first()?->id;

            if ($sppgId) {
                $query->where('sppg_id', $sppgId);
            } else {
                return ['datasets' => [], 'labels' => []];
            }
        }

        // Get aggregated data by date
        $data = $query
            ->select(DB::raw('DATE(received_at) as date_key'), DB::raw('sum(amount) as aggregate'))
            ->groupBy('date_key')
            ->orderBy('date_key')
            ->get();

        // Extract labels and values
        $labels = $data->map(fn ($item) => Carbon::parse($item->date_key)->format('d M Y'));
        $values = $data->pluck('aggregate');

        return [
            'datasets' => [
                [
                    'label' => 'Dana Masuk',
                    'data' => $values,
                    'backgroundColor' => '#10B981', // Green color
                    'borderColor' => '#059669',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
