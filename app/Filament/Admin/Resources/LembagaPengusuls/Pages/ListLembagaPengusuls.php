<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Pages;

use App\Filament\Admin\Resources\LembagaPengusuls\LembagaPengusulResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLembagaPengusuls extends ListRecords
{
    protected static string $resource = LembagaPengusulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
