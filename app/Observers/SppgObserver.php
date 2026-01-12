<?php

namespace App\Observers;

use App\Models\Sppg;
use App\Models\User;

class SppgObserver
{
    /**
     * Handle the Sppg "created" event.
     */
    public function created(Sppg $sppg): void
    {
        $this->syncUserSppg($sppg);
    }

    /**
     * Handle the Sppg "updated" event.
     */
    public function updated(Sppg $sppg): void
    {
        $this->syncUserSppg($sppg);
    }

    protected function syncUserSppg(Sppg $sppg): void
    {
        if ($sppg->pj_id) {
            $user = User::find($sppg->pj_id);
            if ($user && $user->sppg_id !== $sppg->id) {
                $user->update(['sppg_id' => $sppg->id]);
            }
        }

        if ($sppg->kepala_sppg_id) {
            $user = User::find($sppg->kepala_sppg_id);
            if ($user && $user->sppg_id !== $sppg->id) {
                $user->update(['sppg_id' => $sppg->id]);
            }
        }
    }
}
