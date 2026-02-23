<?php

namespace App\Livewire;

use App\Models\OperatingExpense;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperatingExpensesChart extends ChartWidget
{
    protected ?string $heading = 'Biaya Operasional - Grafik';

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole([
            'Kepala SPPG',
            'PJ Pelaksana',
            'Superadmin',
            'Staf Kornas',
            'Ketua Kornas',
        ]);
    }

    protected function getData(): array
    {
        $user = Auth::user();
        $panelId = \Filament\Facades\Filament::getCurrentPanel()->getId();

        // 1. Determine the SPPG ID scope based on panel
        $sppgId = null;
        $shouldShowRent = true;

        if ($panelId === 'sppg') {
            $sppgId = $user->hasRole('Kepala SPPG')
                ? $user->sppgDikepalai?->id
                : $user->unitTugas->first()?->id;
        } elseif ($panelId === 'admin') {
            // Admin panel sees national data (sppg_id = null)
            $sppgId = null;
            $shouldShowRent = false;
        } else {
            return ['datasets' => [], 'labels' => []];
        }

        // 2. Query Operational Expenses
        $opsQuery = OperatingExpense::query()
            ->whereNotNull('date');

        if ($shouldShowRent) {
            // Normal roles filter by specific SPPG ID
            $opsQuery->where('sppg_id', $sppgId);
        } else {
            // Admins filter by NULL SPPG ID
            $opsQuery->whereNull('sppg_id');
        }

        $opsData = $opsQuery
            // Move select() before orderBy() so the alias is available
            ->select(DB::raw('DATE(date) as date_key'), DB::raw('sum(amount) as aggregate'))
            ->groupBy('date_key')
            ->orderBy('date_key') // <--- CHANGE THIS (was 'date')
            ->get();

        // 3. Query Rent Expenses (Only if applicable)
        $rentData = collect(); // Empty collection by default

        if ($shouldShowRent && $sppgId) {
            // Assuming we can access bills via the SPPG model.
            // We use DB::table or a direct Relation query if simpler,
            // but here is the Relation approach assuming the Sppg model is accessible.
            // Using DB Query for efficiency if Sppg model loading is heavy:

            $rentData = DB::table('bills') // Assuming table name is 'bills'
                ->where('sppg_id', $sppgId)
                ->where('type', 'sewa_lokal')
                ->where('status', 'paid')
                ->select(DB::raw('DATE(updated_at) as date_key'), DB::raw('sum(amount) as aggregate'))
                ->groupBy('date_key')
                ->orderBy('date_key')
                ->get();
        }

        // 4. Merge and Sort All Unique Dates (The Master X-Axis)
        $allDates = $opsData->pluck('date_key')
            ->merge($rentData->pluck('date_key'))
            ->unique()
            ->sort()
            ->values();

        // 5. Map Data to the Master Date List
        // This ensures both lines line up perfectly with the labels
        $opsSeries = $allDates->map(function ($date) use ($opsData) {
            return $opsData->firstWhere('date_key', $date)->aggregate ?? 0;
        });

        $rentSeries = $allDates->map(function ($date) use ($rentData) {
            return $rentData->firstWhere('date_key', $date)->aggregate ?? 0;
        });

        // 6. Build Datasets
        $datasets = [
            [
                'label' => 'Biaya Operasional',
                'data' => $opsSeries,
                'borderColor' => '#36A2EB', // Optional styling
            ],
        ];

        if ($shouldShowRent) {
            $datasets[] = [
                'label' => 'Biaya Sewa',
                'data' => $rentSeries,
                'borderColor' => '#FF6384', // Optional styling
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $allDates->map(fn ($date) => Carbon::parse($date)->format('d M Y')),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
