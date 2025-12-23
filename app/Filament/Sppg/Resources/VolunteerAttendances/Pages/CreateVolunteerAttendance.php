<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances\Pages;

use App\Filament\Sppg\Resources\VolunteerAttendances\VolunteerAttendanceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVolunteerAttendance extends CreateRecord
{
    protected static string $resource = VolunteerAttendanceResource::class;
}
