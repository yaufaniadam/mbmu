<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductionVerification;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductionVerificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductionVerification');
    }

    public function view(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('View:ProductionVerification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductionVerification');
    }

    public function update(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('Update:ProductionVerification');
    }

    public function delete(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('Delete:ProductionVerification');
    }

    public function restore(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('Restore:ProductionVerification');
    }

    public function forceDelete(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('ForceDelete:ProductionVerification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductionVerification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductionVerification');
    }

    public function replicate(AuthUser $authUser, ProductionVerification $productionVerification): bool
    {
        return $authUser->can('Replicate:ProductionVerification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductionVerification');
    }

}