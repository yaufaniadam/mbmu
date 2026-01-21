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

    public $activeTab = 'dashboard';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Pimpinan Lembaga Pengusul needs access for:
        // - verify_rent: Menerima pembayaran insentif dari SPPG
        // - pay_royalty: Bayar kontribusi ke Kornas
        if ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
            return true;
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

    public static function shouldRegisterNavigation(): bool
    {
        // Hide for SPPG roles because they now have "PayIncentive" page
        // PJ Pelaksana is now treated as Pimpinan (Foundation Rep), so we show it
        if (auth()->user()?->hasAnyRole(['Kepala SPPG', 'Staf Akuntan'])) {
            return false;
        }

        return true;
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
        } elseif ($user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana'])) {
             if (request()->query('activeTab') === null) {
                $this->activeTab = 'verify_rent';
            }
        } elseif ($user->hasAnyRole(['Kepala SPPG', 'Staf Akuntan'])) {
             if (request()->query('activeTab') === null) {
                $this->activeTab = 'buku_kas';
            }
        }

        // Fallback
        if (! $this->canViewTab($this->activeTab)) {
            foreach (['dashboard', 'buku_kas_pusat', 'buku_kas', 'pay_rent', 'verify_rent', 'pay_royalty', 'verify_royalty', 'transactions'] as $tab) {
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

            'buku_kas' => $user->hasAnyRole(['Superadmin', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']),
            
            'pay_rent' => $user->hasAnyRole(['Kepala SPPG', 'Staf Akuntan']),
            'verify_rent' => $user->hasAnyRole(['Superadmin', 'Pimpinan Lembaga Pengusul', 'PJ Pelaksana']),
            'pay_royalty' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'PJ Pelaksana']),
            'verify_royalty' => $user->hasAnyRole(['Staf Kornas', 'Direktur Kornas']),
            'transactions' => false,
            default => false,
        };
    }
}
