<?php

namespace App\Livewire;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ObservedSppg extends Widget
{
    protected string $view = 'livewire.observed-sppg';

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user->hasRole('Pimpinan Lembaga Pengusul');
    }
}
