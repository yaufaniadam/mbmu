<?php

namespace App\Filament\Lembaga\Resources\SppgResource\Pages;

use App\Filament\Lembaga\Resources\SppgResource;
use Filament\Resources\Pages\ListRecords;

class ListSppgs extends ListRecords
{
    protected static string $resource = SppgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action for Lembaga
        ];
    }
}
