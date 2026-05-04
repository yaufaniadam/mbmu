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
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
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

        // Force HTTPS if using Ngrok or behind secure proxy
        if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https' || str_contains(request()->getHost(), 'ngrok-free.app')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => view('filament.components.panel-switcher')->render(),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn (): string => view('filament.components.debug-footer')->render(),
        );

        // Global reCAPTCHA v3 Implementation
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_START,
            fn(): string => '<meta http-equiv="Content-Security-Policy" content="script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://unpkg.com https://tile.openstreetmap.org https://www.google.com https://www.gstatic.com blob:; worker-src \'self\' blob:; img-src \'self\' data: blob: https:; style-src \'self\' \'unsafe-inline\' https://unpkg.com; frame-src https://www.google.com;">'
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => '<script src="https://www.google.com/recaptcha/api.js?render=' . config('recaptcha.site_key') . '"></script>'
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(): string => '
<script>
(function () {
    var siteKey = "' . config('recaptcha.site_key') . '";
    if (!siteKey) return;

    var currentToken = "";

    function refreshToken() {
        if (typeof grecaptcha === "undefined") {
            setTimeout(refreshToken, 500);
            return;
        }
        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: "login" }).then(function (token) {
                currentToken = token;
            });
        });
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", refreshToken);
    } else {
        refreshToken();
    }
    setInterval(refreshToken, 90000);

    document.addEventListener("livewire:init", function () {
        Livewire.hook("request", function ({ options }) {
            if (!options.headers) options.headers = {};
            options.headers["X-Recaptcha-Token"] = currentToken;
        });
    });
})();
</script>'
        );


    }

}
