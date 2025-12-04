<?php

namespace App\Filament\Pages;

use App\Livewire\BillList;
use App\Livewire\IncomingPayment;
use App\Livewire\TransactionList;
use App\Livewire\VerifyPaymentList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Gate;

class ManageFinance extends Page implements HasForms
{
    use InteractsWithForms;
    protected string $view = 'filament.pages.manage-finance';
    protected ?string $heading = '';
    protected static ?string $navigationLabel = 'Keuangan';

    public $activeTab = 'pay';

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

        if (! $this->canViewTab($this->activeTab)) {
            foreach (['pay', 'transaction', 'verify', 'incoming_payment'] as $tab) {
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
            'pay' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana']),
            'transaction' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana']),
            'verify' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Direktur Kornas']),
            'incoming_payment' => $user->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Staf Kornas', 'Direktur Kornas']),
            default => false,
        };
    }
}
