<?php

namespace App\Filament\Resources\KepalaSppgTokenResource\Pages;

use App\Filament\Resources\KepalaSppgTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListKepalaSppgTokens extends ListRecords
{
    protected static string $resource = KepalaSppgTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions::CreateAction::make(), // Optional: Enable if manual creation is needed
        ];
    }
}
