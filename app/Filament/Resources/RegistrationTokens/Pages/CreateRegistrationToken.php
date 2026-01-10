<?php

namespace App\Filament\Resources\RegistrationTokens\Pages;

use App\Filament\Resources\RegistrationTokens\RegistrationTokenResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRegistrationToken extends CreateRecord
{
    protected static string $resource = RegistrationTokenResource::class;
}
