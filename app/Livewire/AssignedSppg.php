<?php

namespace App\Livewire;

use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class AssignedSppg extends Widget
{
    protected string $view = 'livewire.assigned-sppg';

    public $sppg;

    public function mount()
    {
        $user = Auth::user();

        $this->sppg = User::find($user->id)->getManagedSppg();
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        if (!$user) return false;

        $hasRole = $user->hasAnyRole(['Superadmin', 'Ahli Gizi', 'Staf Gizi', 'Staf Pengantaran', 'Staf Akuntan']);
        
        if (!$hasRole) return false;

        // If not superadmin, check if they actually have an assigned SPPG
        if (!$user->hasRole('Superadmin')) {
            return $user->sppgDiKepalai()->exists() || $user->unitTugas()->exists();
        }

        return true;
    }
}
