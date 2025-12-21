<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\OperatingExpenseCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class OperatingExpenseCategoryPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:OperatingExpenseCategory');
    }

    public function view(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('View:OperatingExpenseCategory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:OperatingExpenseCategory');
    }

    public function update(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('Update:OperatingExpenseCategory');
    }

    public function delete(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('Delete:OperatingExpenseCategory');
    }

    public function restore(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('Restore:OperatingExpenseCategory');
    }

    public function forceDelete(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('ForceDelete:OperatingExpenseCategory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:OperatingExpenseCategory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:OperatingExpenseCategory');
    }

    public function replicate(AuthUser $authUser, OperatingExpenseCategory $operatingExpenseCategory): bool
    {
        return $authUser->can('Replicate:OperatingExpenseCategory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:OperatingExpenseCategory');
    }

}