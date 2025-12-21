<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SppgIncomingFundCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class SppgIncomingFundCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SppgIncomingFundCategory');
    }

    public function view(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('View:SppgIncomingFundCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SppgIncomingFundCategory');
    }

    public function update(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('Update:SppgIncomingFundCategory');
    }

    public function delete(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('Delete:SppgIncomingFundCategory');
    }

    public function restore(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('Restore:SppgIncomingFundCategory');
    }

    public function forceDelete(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('ForceDelete:SppgIncomingFundCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SppgIncomingFundCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SppgIncomingFundCategory');
    }

    public function replicate(AuthUser $authUser, SppgIncomingFundCategory $sppgIncomingFundCategory): bool
    {
        return $authUser->can('Replicate:SppgIncomingFundCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SppgIncomingFundCategory');
    }

}