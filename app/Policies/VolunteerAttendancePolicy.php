<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VolunteerAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VolunteerAttendance');
    }

    public function view(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('View:VolunteerAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VolunteerAttendance');
    }

    public function update(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('Update:VolunteerAttendance');
    }

    public function delete(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('Delete:VolunteerAttendance');
    }

    public function restore(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('Restore:VolunteerAttendance');
    }

    public function forceDelete(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('ForceDelete:VolunteerAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VolunteerAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VolunteerAttendance');
    }

    public function replicate(AuthUser $authUser, VolunteerAttendance $volunteerAttendance): bool
    {
        return $authUser->can('Replicate:VolunteerAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VolunteerAttendance');
    }

}