<?php

namespace App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use UnitEnum;

class ManageFinance extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-finance';

    protected static string|UnitEnum|null $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Keuangan';

    public $activeTab = 'pay';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // 1. Explicitly HIDE from Superadmin (or whatever your admin role slug is)
        // if ($user->hasRole('Superadmin')) {
        //     return false;
        // }

        // 2. Only show to users who actually have tabs to view
        // (This mirrors the logic you used in canViewTab)
        // return $user->hasAnyRole([
        //     'Pimpinan Lembaga Pengusul',
        //     'Kepala SPPG',
        //     'PJ Pelaksana',
        //     'Staf Kornas',
        //     'Direktur Kornas'
        // ]);

        return Gate::allows('View:ManageFinance');
    }

    protected $queryString = [
        'activeTab',
    ];

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-banknotes';
    }

    public function mount(): void
    {
        Gate::authorize('View:ManageFinance');
        $user = auth()->user();

        // 1. ROLE-BASED DEFAULT TAB
        // If the user is Kornas and they haven't clicked a specific tab link (query string is empty),
        // Force the default to be 'verify'.
        if ($user->hasAnyRole(['Staf Kornas', 'Direktur Kornas'])) {
            if (request()->query('activeTab') === null) {
                $this->activeTab = 'verify';
            }
        }

        // 2. PERMISSION FALLBACK (Safety Net)
        // If the current tab (either 'pay' or 'verify') is still forbidden for this user,
        // find the first tab they ARE allowed to see.
        if (! $this->canViewTab($this->activeTab)) {
            foreach (['pay', 'transaction', 'verify', 'incoming_payment', 'operating_expenses'] as $tab) {
                if ($this->canViewTab($tab)) {
                    $this->activeTab = $tab;
                    break;
                }
            }
        }
    }

    protected function canViewTab(string $tab): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return match ($tab) {
            'pay' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']),
            'transaction' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']),
            'verify' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            'incoming_payment' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            'operating_expenses' => $user->hasAnyRole(['Kepala SPPG', 'Staf Akuntan', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            'incoming_funds' => $user->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            default => false,
        };
    }
}
