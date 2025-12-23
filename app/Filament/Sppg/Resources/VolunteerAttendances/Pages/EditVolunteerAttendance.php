<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances\Pages;

use App\Filament\Sppg\Resources\VolunteerAttendances\VolunteerAttendanceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditVolunteerAttendance extends EditRecord
{
    protected static string $resource = VolunteerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
