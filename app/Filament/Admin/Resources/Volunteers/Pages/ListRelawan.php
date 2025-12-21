<?php

namespace App\Filament\Admin\Resources\Volunteers\Pages;

use App\Filament\Admin\Resources\Volunteers\RelawanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRelawan extends ListRecords
{
    protected static string $resource = RelawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
