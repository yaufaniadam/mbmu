<?php

namespace App\Filament\Sppg\Pages;

use App\Livewire\ProductionChart;
use App\Livewire\ProductionDistributionList;
use App\Livewire\ProductionScheduleList;
use App\Livewire\SppgStatsOverview;
use App\Models\Sppg;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Facades\Auth;

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
        ];
    }

    public function getWidgets(): array
    {
        return [
            SppgStatsOverview::class,
            ProductionChart::class,
            ProductionScheduleList::class,
            ProductionDistributionList::class,
        ];
    }

    public function filtersForm(Schema $schema): Schema
    {
        $user = Auth::user();
        $canSeeFilter = $user->hasRole('Pimpinan Lembaga Pengusul') || $user->hasRole('Superadmin');

        // --- 1. Prepare data *before* the form ---
        $sppgOptions = [];
        $defaultSppgId = null;

        if ($canSeeFilter) {
            // Use null-safe operators (?->) to prevent errors
            if (User::find($user->id)->hasRole('Pimpinan Lembaga Pengusul')) {
                $sppgs = $user->lembagaDipimpin?->sppgs;
            } else {
                $sppgs = Sppg::all();
            }

            // Check if we actually got any SPPGs
            if ($sppgs && $sppgs->isNotEmpty()) {
                // 2. Create the options array for the Select
                $sppgOptions = $sppgs->pluck('nama_sppg', 'id');

                // 3. Get the ID of the *first* SPPG in the collection
                $defaultSppgId = $sppgs->first()->id;
            }
        }

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('sppg_id')
                            ->label('SPPG')
                            // 4. Pass the prepared options array
                            ->options($sppgOptions)

                            // 5. Set the default value
                            ->default($defaultSppgId)

                            ->searchable()
                            ->preload(true)
                            ->live(),
                    ])
                    ->columns(1)
                    // Use visible() here, not hidden()
                    ->visible($canSeeFilter),
            ]);
    }
}
