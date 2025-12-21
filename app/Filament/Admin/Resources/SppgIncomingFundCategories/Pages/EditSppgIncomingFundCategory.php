<?php

namespace App\Filament\Admin\Resources\SppgIncomingFundCategories\Pages;

use App\Filament\Admin\Resources\SppgIncomingFundCategories\SppgIncomingFundCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditSppgIncomingFundCategory extends EditRecord
{
    protected static string $resource = SppgIncomingFundCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
