<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Pages;

use App\Filament\Resources\SppgFinancialReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSppgFinancialReport extends EditRecord
{
    protected static string $resource = SppgFinancialReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
