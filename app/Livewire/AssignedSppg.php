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

        if ($user->hasRole('Kepala SPPG')) {
            $this->sppg = User::find($user->id)->sppgDikepalai;
        } else {
            $this->sppg = User::find($user->id)->unitTugas->first();
        }
    }

    public static function canView(): bool
    {
        $user = Auth::user();

        return $user->hasAnyRole(['Superadmin', 'Ahli Gizi', 'Staf Gizi', 'Staf Pengantaran', 'Staf Akuntan']);
    }
}
