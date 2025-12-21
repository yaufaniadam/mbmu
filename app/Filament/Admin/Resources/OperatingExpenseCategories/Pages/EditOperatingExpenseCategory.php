<?php

namespace App\Filament\Admin\Resources\OperatingExpenseCategories\Pages;

use App\Filament\Admin\Resources\OperatingExpenseCategories\OperatingExpenseCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditOperatingExpenseCategory extends EditRecord
{
    protected static string $resource = OperatingExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\DeleteAction::make(),
        ];
    }
}
