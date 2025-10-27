<?php

namespace App\Policies;

use App\Models\Sppg;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SppgPolicy
{
    use HandlesAuthorization;

    /**
     * Memberikan izin super kepada peran-peran level nasional.
     * Method ini berjalan sebelum pengecekan izin lainnya.
     *
     * @param \App\Models\User $user
     * @param string $ability
     * @return bool|null
     */
    public function before(User $user, $ability)
    {
        // Jika user punya izin 'manage-sppg' (seperti Staf Kornas) atau adalah Superadmin,
        // maka dia bisa melakukan aksi apapun terkait SPPG.
        if ($user->can('manage-sppg') || $user->hasRole('Superadmin')) {
            return true;
        }

        return null; // Lanjutkan ke pengecekan izin spesifik jika bukan
    }

    /**
     * Menentukan apakah user bisa melihat detail SPPG.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Sppg $sppg
     * @return bool
     */
    public function view(User $user, Sppg $sppg): bool
    {
        // User bisa melihat SPPG jika dia adalah kepala SPPG tersebut
        if ($sppg->kepala_sppg_id === $user->id) {
            return true;
        }
        
        // ATAU jika dia punya salah satu dari peran-peran ini KHUSUS untuk SPPG ini.
        return $user->hasRole([
            'Kepala SPPG',
            'Staf Administrator SPPG',
            'Staf Gizi',
            'Staf Pengantaran',
            'Staf Akuntan',
            'PJ Pelaksana',         // <-- Peran baru ditambahkan
            'Penerima Kuasa',       // <-- Peran baru ditambahkan
        ], $sppg->id);
    }

    /**
     * Menentukan apakah user bisa mengupdate data SPPG.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Sppg $sppg
     * @return bool
     */
    public function update(User $user, Sppg $sppg): bool
    {
        // Hanya Kepala SPPG atau Staf Administrator dari SPPG tersebut yang boleh mengedit.
        if ($sppg->kepala_sppg_id === $user->id) {
            return true;
        }

        return $user->hasRole(['Kepala SPPG', 'Staf Administrator SPPG'], $sppg->id);
    }

    /**
     * Menentukan apakah user bisa menghapus SPPG.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Sppg $sppg
     * @return bool
     */
    public function delete(User $user, Sppg $sppg): bool
    {
        // Aksi menghapus sangat krusial, hanya boleh dilakukan oleh Kepala SPPG.
        // (Peran Kornas/Superadmin sudah ditangani oleh method 'before').
        if ($sppg->kepala_sppg_id === $user->id) {
            return true;
        }
        
        return $user->hasRole('Kepala SPPG', $sppg->id);
    }
}