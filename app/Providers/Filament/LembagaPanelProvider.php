<?php

namespace App\Providers\Filament;

use App\Http\Middleware\CanAccessLembagaPanel;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class LembagaPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('lembaga')
            ->path('lembaga')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandLogo(asset('logombm-small.png'))
            ->darkModeBrandLogo(asset('logombm-w.png'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Lembaga/Resources'), for: 'App\Filament\Lembaga\Resources')
            ->discoverPages(in: app_path('Filament/Lembaga/Pages'), for: 'App\Filament\Lembaga\Pages')
            ->pages([
                \App\Filament\Lembaga\Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Lembaga/Widgets'), for: 'App\Filament\Lembaga\Widgets')
            ->widgets([
                // Widgets will be added as needed
            ])
            ->navigationGroups([
                'Operasional',
                'Keuangan',
                'Kelembagaan',
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
                CanAccessLembagaPanel::class,
            ])
            ->spa(hasPrefetching: true)
            ->databaseNotifications();
    }
}
