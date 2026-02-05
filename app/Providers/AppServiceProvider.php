<?php

namespace App\Providers;

use App\Models\OperatingExpense;
use App\Models\Remittance;
use App\Models\SppgIncomingFund;
use App\Observers\FinancialObserver;
use App\Observers\SppgObserver;
use App\Models\Sppg;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();
        
        FilamentAsset::register([
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
        ]);

        SppgIncomingFund::observe(FinancialObserver::class);
        OperatingExpense::observe(FinancialObserver::class);
        Remittance::observe(FinancialObserver::class);
        Sppg::observe(SppgObserver::class);

        // Superadmin Bypass
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Superadmin') ? true : null;
        });
    }
}
