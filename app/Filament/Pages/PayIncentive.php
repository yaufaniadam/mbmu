<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Gate;
use UnitEnum;
use BackedEnum;

class PayIncentive extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected string $view = 'filament.pages.pay-incentive';

    protected static string|UnitEnum|null $navigationGroup = 'Keuangan';

    protected static ?string $title = 'Bayar Insentif';

    protected static ?string $navigationLabel = 'Bayar Insentif';

    protected static ?int $navigationSort = 3;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->hasAnyRole(['Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan']);
    }

    public static function getNavigationBadge(): ?string
    {
        $user = auth()->user();
        if (!$user) return null;

        // Get count of unpaid invoices for the user's SPPG
        $sppg = $user->sppgDiKepalai ?? $user->sppg;
        if (!$sppg) return null;

        $count = \App\Models\Invoice::where('sppg_id', $sppg->id)
            ->where('type', 'SPPG_SEWA')
            ->where('status', 'UNPAID')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
