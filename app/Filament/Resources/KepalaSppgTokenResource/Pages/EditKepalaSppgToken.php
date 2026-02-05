<?php

namespace App\Filament\Resources\KepalaSppgTokenResource\Pages;

use App\Filament\Resources\KepalaSppgTokenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKepalaSppgToken extends EditRecord
{
    protected static string $resource = KepalaSppgTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
