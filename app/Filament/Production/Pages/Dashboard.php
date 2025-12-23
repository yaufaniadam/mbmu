<?php

namespace App\Filament\Production\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?int $navigationSort = -2;

    public static function shouldRegisterNavigation(): bool
    {
        // Hide dashboard for Staf Pengantaran
        $user = auth()->user();
        if ($user && $user->hasRole('Staf Pengantaran')) {
            return false;
        }
        
        return true;
    }

    public function mount(): void
    {
        // Redirect Staf Pengantaran to Distribution page
        $user = auth()->user();
        if ($user && $user->hasRole('Staf Pengantaran')) {
            $this->redirect(Distribution::getUrl());
            return;
        }
    }
}
