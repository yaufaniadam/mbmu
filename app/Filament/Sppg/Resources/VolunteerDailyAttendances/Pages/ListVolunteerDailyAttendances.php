<?php

namespace App\Filament\Sppg\Resources\VolunteerDailyAttendances\Pages;

use App\Filament\Sppg\Resources\VolunteerDailyAttendances\VolunteerDailyAttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerDailyAttendances extends ListRecords
{
    protected static string $resource = VolunteerDailyAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
