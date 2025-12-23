<?php

namespace App\Filament\Sppg\Resources\VolunteerAttendances\Pages;

use App\Filament\Sppg\Resources\VolunteerAttendances\VolunteerAttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolunteerAttendances extends ListRecords
{
    protected static string $resource = VolunteerAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
