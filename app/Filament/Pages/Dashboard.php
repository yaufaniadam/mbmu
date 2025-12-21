<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

use App\Livewire\OsmMapWidget;
use App\Livewire\ProductionChart;
use App\Livewire\ProductionDistributionList;
use App\Livewire\ProductionScheduleList;
use App\Livewire\SppgStatsOverview;
use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;

    public function getColumns(): int|array
    {
        return 2;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    public function getFiltersWidgetColumnSpan(): int|array
    {
        return 'full';
    }

    public function getHeaderWidgets(): array
    {
        return [
            AccountWidget::class,
            OsmMapWidget::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Admin\Widgets\EfficiencyMonitorWidget::class,
            SppgStatsOverview::class,
            \App\Filament\Admin\Widgets\SppgFinancialRadar::class,
            ProductionChart::class,
            ProductionScheduleList::class,
            ProductionDistributionList::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // National roles see Province Filter
        $nationalRoles = ['Superadmin', 'Staf Kornas', 'Direktur Kornas'];
        $isNational = $user->hasAnyRole($nationalRoles);
        $isPimpinan = $user->hasRole('Pimpinan Lembaga Pengusul');

        $sppgOptions = [];
        if ($isNational) {
            $sppgOptions = Sppg::pluck('nama_sppg', 'id');
        } elseif ($isPimpinan) {
            // Pimpinan sees their own SPPGs
            $sppgOptions = User::find($user->id)->lembagaDipimpin?->sppgs->pluck('nama_sppg', 'id') ?? [];
        }

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('province_code')
                            ->label('Filter peta SPPG per provinsi')
                            ->options(fn() => DB::table('indonesia_provinces')->pluck('name', 'code'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn() => $this->dispatch('refresh-map-widget'))
                            ->visible($isNational),

                        Select::make('sppg_id')
                            ->label('SPPG')
                            ->placeholder('Semua SPPG (Nasional)')
                            ->options($sppgOptions)
                            ->searchable()
                            ->preload(true)
                            ->live()
                            ->afterStateUpdated(fn() => $this->dispatch('refresh-map-widget')),
                    ])
                    ->columns(1)
                    ->visible($isNational || $isPimpinan),
            ]);
    }
}
