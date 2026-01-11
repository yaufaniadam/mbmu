<?php

namespace App\Filament\Admin\Resources\HomeSliders\Pages;

use App\Filament\Admin\Resources\HomeSliders\HomeSliderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeSliders extends ListRecords
{
    protected static string $resource = HomeSliderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
