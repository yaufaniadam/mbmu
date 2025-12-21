<?php

namespace App\Filament\Sppg\Resources\Schools\Pages;

use App\Filament\Sppg\Resources\Schools\SchoolResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSchool extends ViewRecord
{
    protected static string $resource = SchoolResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
