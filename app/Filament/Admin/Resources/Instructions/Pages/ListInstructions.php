<?php

namespace App\Filament\Admin\Resources\Instructions\Pages;

use App\Filament\Admin\Resources\Instructions\InstructionResource;
use Filament\Resources\Pages\ListRecords;

class ListInstructions extends ListRecords
{
    protected static string $resource = InstructionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\CreateAction::make(),
        ];
    }
}
