<?php

namespace App\Filament\Resources\Sppgs\Pages;

use App\Filament\Resources\Sppgs\SppgResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSppg extends EditRecord
{
    protected static string $resource = SppgResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
