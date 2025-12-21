<?php

namespace App\Filament\Resources\ProductionSchedules\Pages;

use App\Filament\Resources\ProductionSchedules\ProductionScheduleResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductionSchedule extends ViewRecord
{
    protected static string $resource = ProductionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
