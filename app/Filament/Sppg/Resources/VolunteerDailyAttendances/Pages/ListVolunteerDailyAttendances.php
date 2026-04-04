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
            \Filament\Actions\Action::make('presensi')
                ->label('Presensi')
                ->icon('heroicon-o-clipboard-document-check')
                ->color('primary')
                ->url(fn (): string => \App\Filament\Sppg\Pages\DailyAttendance::getUrl()),
            CreateAction::make(),
        ];
    }
}
