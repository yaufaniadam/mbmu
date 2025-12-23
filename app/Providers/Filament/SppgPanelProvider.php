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
            ->brandLogo(asset('logombm-small.png'))
            ->darkModeBrandLogo(asset('logombm-w.png'))
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::Amber,
            ])
            // ->discoverResources(in: app_path('Filament/Resources/Sppgs'), for: 'App\Filament\Resources\Sppgs')
            ->resources([
                ProductionScheduleResource::class,
            ])
            ->discoverResources(in: app_path('Filament/Sppg/Resources'), for: 'App\Filament\Sppg\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
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
            ->spa(hasPrefetching: true);
    }
}
