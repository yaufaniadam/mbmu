<?php

namespace App\Filament\Admin\Resources\Volunteers\Pages;

use App\Filament\Admin\Resources\Volunteers\RelawanResource;
use App\Filament\Imports\VolunteerImporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;

class ListRelawan extends ListRecords
{
    protected static string $resource = RelawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make()
                ->importer(VolunteerImporter::class),
            CreateAction::make(),
        ];
    }
}
