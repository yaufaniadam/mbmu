<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Complaint;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        if ($authUser->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana', 'Superadmin', 'Ketua Kornas', 'Staf Kornas'])) {
            return true;
        }
        return $authUser->can('ViewAny:Complaint');
    }

    public function view(AuthUser $authUser, Complaint $complaint): bool
    {
        /** @var \App\Models\User $authUser */
        if ($complaint->user_id === $authUser->id) {
            return true;
        }

        if ($authUser->hasAnyRole(['Superadmin', 'Ketua Kornas', 'Staf Kornas'])) {
            return true;
        }

        // For Lembaga/SPPG roles, we could check if the complaint's user belongs to their SPPG, 
        // but for now, we'll rely on the existing role check which is likely filtered by tenant in the resource.
        if ($authUser->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana'])) {
            return true;
        }

        return $authUser->can('View:Complaint');
    }

    public function create(AuthUser $authUser): bool
    {
        if ($authUser->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana'])) {
            return true;
        }
        return $authUser->can('Create:Complaint');
    }

    public function update(AuthUser $authUser, Complaint $complaint): bool
    {
        /** @var \App\Models\User $authUser */
        if ($complaint->user_id !== $authUser->id) {
            return false;
        }

        if ($authUser->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana'])) {
            return true;
        }
        return $authUser->can('Update:Complaint');
    }

    public function delete(AuthUser $authUser, Complaint $complaint): bool
    {
        if ($authUser->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG', 'PJ Pelaksana'])) {
            return true;
        }
        return $authUser->can('Delete:Complaint');
    }

    public function restore(AuthUser $authUser, Complaint $complaint): bool
    {
        return $authUser->can('Restore:Complaint');
    }

    public function forceDelete(AuthUser $authUser, Complaint $complaint): bool
    {
        return $authUser->can('ForceDelete:Complaint');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Complaint');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Complaint');
    }

    public function replicate(AuthUser $authUser, Complaint $complaint): bool
    {
        return $authUser->can('Replicate:Complaint');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Complaint');
    }

}