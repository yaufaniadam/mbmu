<?php

namespace App\Filament\Sppg\Pages;

use App\Livewire\OsmMapWidget;
use App\Livewire\ProductionChart;
use App\Livewire\ProductionDistributionList;
use App\Livewire\ProductionScheduleList;
use App\Livewire\SppgStatsOverview;
use App\Livewire\SppgTopStatsWidget;
use App\Livewire\FinanceStatsOverview;
use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends BaseDashboard
{
    // protected string $view = 'filament.pages.dashboard';
    use HasFiltersForm;

    public function getColumns(): int|array
    {
        return 2;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1; // Now the header has a 12-column grid
    }

    public function getFiltersWidgetColumnSpan(): int|array
    {
        return 'full'; // or 2
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
            SppgTopStatsWidget::class,
            SppgStatsOverview::class,
            ProductionChart::class,
            ProductionScheduleList::class,
            ProductionDistributionList::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Define the roles allowed to see the Map Widget (and thus the Province Filter)
        $mapRoles = ['Superadmin', 'Staf Kornas', 'Ketua Kornas'];

        // Check if user has ANY of these roles
        $isMapRole = $user->hasAnyRole($mapRoles);

        // Check if user is Pimpinan or PJ Pelaksana
        $isPjPelaksana = $user->hasRole('PJ Pelaksana');
        $isPimpinan = $user->hasRole('Pimpinan Lembaga Pengusul') || $isPjPelaksana;

        // 2. Section Visibility: 
        // - Hide for PJ Pelaksana & Kepala SPPG (only 1 SPPG)
        // - Show for Map Roles (all SPPGs)
        // - Show for Pimpinan Lembaga (might have multiple SPPGs)
        $isKepalaSppg = $user->hasRole('Kepala SPPG');
        $canSeeSection = ($isMapRole || ($isPimpinan && !$isPjPelaksana)) && !$isKepalaSppg;

        // --- Prepare SPPG Data ---
        $sppgOptions = [];
        $defaultSppgId = null;

        if ($isMapRole || $isPimpinan || $isKepalaSppg) {
            if ($isMapRole) {
                $sppgs = Sppg::all();
            } elseif ($isKepalaSppg) {
                 $sppgs = collect([$user->getManagedSppg()]);
            } else {
                // Pimpinan or PJ (though PJ is now hidden, we keep logic for default value)
                $sppgs = User::find($user->id)->lembagaDipimpin?->sppgs;
                
                // Fallback for PJ if needed
                if (!$sppgs && $isPjPelaksana) {
                    $sppgs = collect([$user->getManagedSppg()]);
                }
            }

            if ($sppgs && $sppgs->isNotEmpty()) {
                $sppgOptions = $sppgs->pluck('nama_sppg', 'id');
                $defaultSppgId = $sppgs->filter()->first()?->id;
            }
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
                            // VISIBILITY RESTRICTION: Only Map Roles can see this
                            ->visible($isMapRole),

                        Select::make('sppg_id')
                            ->label('SPPG')
                            ->options($sppgOptions)
                            ->default($defaultSppgId)
                            ->searchable()
                            ->preload(true)
                            ->live()
                            ->afterStateUpdated(fn() => $this->dispatch('refresh-map-widget')),
                    ])
                    ->columns(1)
                    ->visible($canSeeSection),
            ]);
    }
}
