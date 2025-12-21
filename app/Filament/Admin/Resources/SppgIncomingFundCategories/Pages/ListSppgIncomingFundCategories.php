<?php

namespace App\Filament\Admin\Resources\SppgIncomingFundCategories\Pages;

use App\Filament\Admin\Resources\SppgIncomingFundCategories\SppgIncomingFundCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSppgIncomingFundCategories extends ListRecords
{
    protected static string $resource = SppgIncomingFundCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
