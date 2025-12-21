<?php

namespace App\Filament\Admin\Resources\OperatingExpenseCategories\Pages;

use App\Filament\Admin\Resources\OperatingExpenseCategories\OperatingExpenseCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOperatingExpenseCategories extends ListRecords
{
    protected static string $resource = OperatingExpenseCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
