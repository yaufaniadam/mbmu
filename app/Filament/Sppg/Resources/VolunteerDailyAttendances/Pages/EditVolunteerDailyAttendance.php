<?php

namespace App\Filament\Sppg\Resources\VolunteerDailyAttendances\Pages;

use App\Filament\Sppg\Resources\VolunteerDailyAttendances\VolunteerDailyAttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerDailyAttendance extends EditRecord
{
    protected static string $resource = VolunteerDailyAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
