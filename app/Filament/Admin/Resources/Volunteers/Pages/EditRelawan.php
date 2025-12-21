<?php

namespace App\Filament\Admin\Resources\Volunteers\Pages;

use App\Filament\Admin\Resources\Volunteers\RelawanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditRelawan extends EditRecord
{
    protected static string $resource = RelawanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
