<?php

namespace App\Filament\Admin\Resources\OperatingExpenseCategories\Pages;

use App\Filament\Admin\Resources\OperatingExpenseCategories\OperatingExpenseCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOperatingExpenseCategory extends CreateRecord
{
    protected static string $resource = OperatingExpenseCategoryResource::class;
}
