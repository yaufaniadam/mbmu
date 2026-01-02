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

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Keuangan';
    protected static bool $shouldRegisterNavigation = false;

    public $activeTab = 'dashboard';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->hasRole('Pimpinan Lembaga Pengusul')) {
            return false;
        }

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

        // Default tab based on role
        if ($user->hasAnyRole(['Staf Kornas', 'Direktur Kornas'])) {
            if (request()->query('activeTab') === null) {
                // Kornas defaults to their own Buku Kas Pusat
                $this->activeTab = 'buku_kas_pusat';
            }
        } elseif ($user->hasRole('Pimpinan Lembaga Pengusul')) {
             if (request()->query('activeTab') === null) {
                $this->activeTab = 'verify_rent';
            }
        } elseif ($user->hasAnyRole(['Kepala SPPG', 'Staf Akuntan', 'PJ Pelaksana'])) {
             if (request()->query('activeTab') === null) {
                $this->activeTab = 'buku_kas';
            }
        }

        // Fallback
        if (! $this->canViewTab($this->activeTab)) {
            foreach (['dashboard', 'buku_kas_pusat', 'audit_sppg', 'buku_kas', 'pay_rent', 'verify_rent', 'pay_royalty', 'verify_royalty', 'transactions'] as $tab) {
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
            'dashboard' => $user->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            
            // 1. Buku Kas Pusat (Kornas Only)
            'buku_kas_pusat' => $user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            'audit_sppg' => $user->hasAnyRole(['Superadmin', 'Staf Kornas', 'Staf Akuntan Kornas', 'Direktur Kornas']),
            'buku_kas' => $user->hasAnyRole(['Superadmin', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']),
            
            'pay_rent' => $user->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']),
            'verify_rent' => $user->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul']),
            'pay_royalty' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul']),
            'verify_royalty' => $user->hasAnyRole(['Staf Kornas', 'Direktur Kornas']),
            'transactions' => false,
            default => false,
        };
    }
}
