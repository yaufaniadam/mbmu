<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ManageFinance;
use App\Filament\Pages\ProductionVerificationSetting;
use App\Filament\Sppg\Pages\Dashboard;
use App\Http\Middleware\CanAccessAdminPanel;
use App\Livewire\OsmMapWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->brandLogo(asset('logombm-small.png'))
            ->darkModeBrandLogo(asset('logombm-w.png'))
            ->brandLogoHeight('3rem')
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resources([
                \App\Filament\Admin\Resources\Schools\AdminSchoolResource::class,
                \App\Filament\Admin\Resources\Volunteers\RelawanResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
                ManageFinance::class,
                ProductionVerificationSetting::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([
                AccountWidget::class,
                // OsmMapWidget::class, // Moved to Dashboard
                // FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CanAccessAdminPanel::class,
            ])
            ->spa(hasPrefetching: true)
            ->renderHook(
                'panels::head.start',
                fn(): string => '<meta http-equiv="Content-Security-Policy" content="script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://unpkg.com https://tile.openstreetmap.org; style-src \'self\' \'unsafe-inline\' https://unpkg.com;">'
            )
            ->renderHook(
                'panels::head.end',
                fn(): string => '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />'
            )
            ->renderHook(
                'panels::body.end',
                fn(): string => '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>'
            );
    }
}
