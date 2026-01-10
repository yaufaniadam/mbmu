<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SppgFinancialReport;
use Illuminate\Auth\Access\HandlesAuthorization;

class SppgFinancialReportPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SppgFinancialReport');
    }

    public function view(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('View:SppgFinancialReport');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SppgFinancialReport');
    }

    public function update(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('Update:SppgFinancialReport');
    }

    public function delete(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('Delete:SppgFinancialReport');
    }

    public function restore(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('Restore:SppgFinancialReport');
    }

    public function forceDelete(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('ForceDelete:SppgFinancialReport');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SppgFinancialReport');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SppgFinancialReport');
    }

    public function replicate(AuthUser $authUser, SppgFinancialReport $sppgFinancialReport): bool
    {
        return $authUser->can('Replicate:SppgFinancialReport');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SppgFinancialReport');
    }

}