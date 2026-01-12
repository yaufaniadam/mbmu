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
}
