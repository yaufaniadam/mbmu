<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Pages;

use App\Filament\Resources\SppgFinancialReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSppgFinancialReports extends ListRecords
{
    protected static string $resource = SppgFinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
