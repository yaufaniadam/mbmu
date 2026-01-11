<?php

namespace App\Filament\Admin\Resources\HomeFeatures\Pages;

use App\Filament\Admin\Resources\HomeFeatures\HomeFeatureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHomeFeature extends EditRecord
{
    protected static string $resource = HomeFeatureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
