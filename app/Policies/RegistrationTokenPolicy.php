<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RegistrationToken;
use Illuminate\Auth\Access\HandlesAuthorization;

class RegistrationTokenPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RegistrationToken');
    }

    public function view(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('View:RegistrationToken');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RegistrationToken');
    }

    public function update(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('Update:RegistrationToken');
    }

    public function delete(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('Delete:RegistrationToken');
    }

    public function restore(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('Restore:RegistrationToken');
    }

    public function forceDelete(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('ForceDelete:RegistrationToken');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RegistrationToken');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RegistrationToken');
    }

    public function replicate(AuthUser $authUser, RegistrationToken $registrationToken): bool
    {
        return $authUser->can('Replicate:RegistrationToken');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RegistrationToken');
    }

}