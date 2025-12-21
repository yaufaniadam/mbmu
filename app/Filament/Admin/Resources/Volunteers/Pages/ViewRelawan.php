<?php

namespace App\Filament\Admin\Resources\Volunteers\Pages;

use App\Filament\Admin\Resources\Volunteers\RelawanResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRelawan extends ViewRecord
{
    protected static string $resource = RelawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
