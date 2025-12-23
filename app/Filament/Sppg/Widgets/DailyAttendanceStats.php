<?php

namespace App\Filament\Sppg\Widgets;

use App\Models\VolunteerDailyAttendance;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Sppg\Pages\DailyAttendance;
use Illuminate\Support\Facades\Auth;

class DailyAttendanceStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    // Public property to accept the data passed from the Page
    public ?string $selectedDate = null;

    protected function getStats(): array
    {
        $date = $this->selectedDate ?? now()->format('Y-m-d');

        $user = Auth::user();
        $sppgId = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppgId = $user->sppgDikepalai?->id;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $sppgId = $user->unitTugas->first()?->id;
        }

        $query = VolunteerDailyAttendance::query()
            ->where('attendance_date', $date);

        if ($sppgId) {
            $query->where('sppg_id', $sppgId);
        }

        $stats = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            Stat::make('Hadir', $stats['Hadir'] ?? 0)
                ->color('success'),
            Stat::make('Izin', $stats['Izin'] ?? 0)
                ->color('warning'),
            Stat::make('Sakit', $stats['Sakit'] ?? 0)
                ->color('danger'),
            Stat::make('Alpha', $stats['Alpha'] ?? 0)
                ->color('danger'),
        ];
    }
}
