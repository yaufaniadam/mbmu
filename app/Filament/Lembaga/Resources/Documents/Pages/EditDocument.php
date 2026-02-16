<?php

namespace App\Filament\Lembaga\Resources\Documents\Pages;

use App\Filament\Lembaga\Resources\Documents\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
