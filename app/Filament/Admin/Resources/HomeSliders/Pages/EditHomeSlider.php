<?php

namespace App\Filament\Admin\Resources\HomeSliders\Pages;

use App\Filament\Admin\Resources\HomeSliders\HomeSliderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeSlider extends EditRecord
{
    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
