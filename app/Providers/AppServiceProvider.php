<?php

namespace App\Providers;

use App\Models\OperatingExpense;
use App\Models\Remittance;
use App\Models\SppgIncomingFund;
use App\Observers\FinancialObserver;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
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
        FilamentAsset::register([
            Css::make('leaflet-css', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet-js', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'),
        ]);

        SppgIncomingFund::observe(FinancialObserver::class);
        OperatingExpense::observe(FinancialObserver::class);
        Remittance::observe(FinancialObserver::class);
    }
}
