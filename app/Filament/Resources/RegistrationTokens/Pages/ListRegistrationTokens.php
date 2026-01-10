<?php

namespace App\Filament\Resources\RegistrationTokens\Pages;

use App\Filament\Resources\RegistrationTokens\RegistrationTokenResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRegistrationTokens extends ListRecords
{
    protected static string $resource = RegistrationTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
