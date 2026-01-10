<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VolunteerDailyAttendance;
use Illuminate\Auth\Access\HandlesAuthorization;

class VolunteerDailyAttendancePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VolunteerDailyAttendance');
    }

    public function view(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('View:VolunteerDailyAttendance');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VolunteerDailyAttendance');
    }

    public function update(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('Update:VolunteerDailyAttendance');
    }

    public function delete(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('Delete:VolunteerDailyAttendance');
    }

    public function restore(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('Restore:VolunteerDailyAttendance');
    }

    public function forceDelete(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('ForceDelete:VolunteerDailyAttendance');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VolunteerDailyAttendance');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VolunteerDailyAttendance');
    }

    public function replicate(AuthUser $authUser, VolunteerDailyAttendance $volunteerDailyAttendance): bool
    {
        return $authUser->can('Replicate:VolunteerDailyAttendance');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VolunteerDailyAttendance');
    }

}