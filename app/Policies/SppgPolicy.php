<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Sppg;
use Illuminate\Auth\Access\HandlesAuthorization;

class SppgPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Sppg');
    }

    public function view(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('View:Sppg');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Sppg');
    }

    public function update(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('Update:Sppg');
    }

    public function delete(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('Delete:Sppg');
    }

    public function restore(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('Restore:Sppg');
    }

    public function forceDelete(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('ForceDelete:Sppg');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Sppg');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Sppg');
    }

    public function replicate(AuthUser $authUser, Sppg $sppg): bool
    {
        return $authUser->can('Replicate:Sppg');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Sppg');
    }

}