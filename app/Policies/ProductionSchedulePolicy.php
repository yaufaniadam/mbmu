<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ProductionSchedule;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductionSchedulePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ProductionSchedule');
    }

    public function view(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('View:ProductionSchedule');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ProductionSchedule');
    }

    public function update(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('Update:ProductionSchedule');
    }

    public function delete(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('Delete:ProductionSchedule');
    }

    public function restore(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('Restore:ProductionSchedule');
    }

    public function forceDelete(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('ForceDelete:ProductionSchedule');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ProductionSchedule');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ProductionSchedule');
    }

    public function replicate(AuthUser $authUser, ProductionSchedule $productionSchedule): bool
    {
        return $authUser->can('Replicate:ProductionSchedule');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ProductionSchedule');
    }

}