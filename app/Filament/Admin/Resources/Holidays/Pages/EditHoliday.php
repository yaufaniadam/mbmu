<?php

namespace App\Filament\Admin\Resources\Holidays\Pages;

use App\Filament\Admin\Resources\Holidays\HolidayResource;
use Filament\Resources\Pages\EditRecord;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
