<?php

namespace App\Filament\Sppg\Resources\ProductionVerificationResource\Pages;

use App\Filament\Sppg\Resources\ProductionVerificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductionVerifications extends ListRecords
{
    protected static string $resource = ProductionVerificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Evaluasi Baru'),
        ];
    }
}
