<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\LembagaPengusul;
use Illuminate\Auth\Access\HandlesAuthorization;

class LembagaPengusulPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:LembagaPengusul');
    }

    public function view(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('View:LembagaPengusul');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:LembagaPengusul');
    }

    public function update(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('Update:LembagaPengusul');
    }

    public function delete(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('Delete:LembagaPengusul');
    }

    public function restore(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('Restore:LembagaPengusul');
    }

    public function forceDelete(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('ForceDelete:LembagaPengusul');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:LembagaPengusul');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:LembagaPengusul');
    }

    public function replicate(AuthUser $authUser, LembagaPengusul $lembagaPengusul): bool
    {
        return $authUser->can('Replicate:LembagaPengusul');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:LembagaPengusul');
    }

}