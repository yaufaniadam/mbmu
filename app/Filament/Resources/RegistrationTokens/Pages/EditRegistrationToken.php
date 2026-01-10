<?php

namespace App\Filament\Resources\RegistrationTokens\Pages;

use App\Filament\Resources\RegistrationTokens\RegistrationTokenResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRegistrationToken extends EditRecord
{
    protected static string $resource = RegistrationTokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
