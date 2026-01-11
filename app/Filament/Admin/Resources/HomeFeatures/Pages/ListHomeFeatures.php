<?php

namespace App\Filament\Admin\Resources\HomeFeatures\Pages;

use App\Filament\Admin\Resources\HomeFeatures\HomeFeatureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHomeFeatures extends ListRecords
{
    protected static string $resource = HomeFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
