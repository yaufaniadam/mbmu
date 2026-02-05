<?php

namespace App\Providers\Filament;

use App\Filament\Pages\ManageFinance;
use App\Filament\Resources\ProductionSchedules\ProductionScheduleResource;
use App\Filament\Sppg\Pages\Dashboard;
use App\Filament\Sppg\Pages\DailyAttendance;
use App\Filament\Sppg\Pages\SppgProfile;
use App\Http\Middleware\CanAccessSppgPanel;
use App\Livewire\AssignedSppg;
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

class SppgPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('sppg')
            ->path('sppg')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandLogo(asset('logombm-small.png'))
            ->darkModeBrandLogo(asset('logombm-w.png'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            // ->discoverResources(in: app_path('Filament/Resources/Sppgs'), for: 'App\Filament\Resources\Sppgs')
            ->resources([
                ProductionScheduleResource::class,
                \App\Filament\Resources\SppgFinancialReportResource::class,
                \App\Filament\Resources\PostResource::class,
                \App\Filament\Resources\ComplaintResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Sppg/Resources'), for: 'App\Filament\Sppg\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->discoverPages(in: app_path('Filament/Sppg/Pages'), for: 'App\Filament\Sppg\Pages')
            ->pages([
                Dashboard::class,
                DailyAttendance::class,
                SppgProfile::class,
                ManageFinance::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->discoverWidgets(in: app_path('Filament/Sppg/Widgets'), for: 'App\Filament\Sppg\Widgets')
            ->widgets([
                // AccountWidget::class,
                // AssignedSppg::class,
                // SppgOverview::class,
                // FilamentInfoWidget::class,
            ])
            ->navigationGroups([
                'Operasional',
                'Data Master',
                'Pengaturan Situs',
                'Pengaturan',
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
                CanAccessSppgPanel::class,
            ])
            ->spa(hasPrefetching: true)
            ->renderHook(
                'panels::head.start',
                fn(): string => '<meta http-equiv="Content-Security-Policy" content="script-src \'self\' \'unsafe-inline\' \'unsafe-eval\' https://unpkg.com https://tile.openstreetmap.org blob:; worker-src \'self\' blob:; img-src \'self\' data: blob: https:; style-src \'self\' \'unsafe-inline\' https://unpkg.com;">'
            )
            ->renderHook(
                'panels::head.end',
                fn(): string => '<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />'
            )
            ->renderHook(
                'panels::body.end',
                fn(): string => '<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>'
            )
            ->databaseNotifications();
    }
}
