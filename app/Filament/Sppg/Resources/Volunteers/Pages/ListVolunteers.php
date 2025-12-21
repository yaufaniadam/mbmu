<?php

namespace App\Filament\Sppg\Resources\Volunteers\Pages;

use App\Filament\Sppg\Resources\Volunteers\VolunteerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVolunteers extends ListRecords
{
    protected static string $resource = VolunteerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
