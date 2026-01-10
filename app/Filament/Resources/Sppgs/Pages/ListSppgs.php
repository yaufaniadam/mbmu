<?php

namespace App\Filament\Resources\Sppgs\Pages;

use App\Filament\Resources\Sppgs\SppgResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSppgs extends ListRecords
{
    protected static string $resource = SppgResource::class;
    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\ImportAction::make()
                ->importer(\App\Filament\Imports\SppgImporter::class)
                ->label('Import SPPG (Excel)')
                ->modalHeading('Import Data SPPG')
                ->modalDescription('Silakan upload file Excel (.xlsx) atau CSV.')
                ->options([
                    'updateExisting' => true,
                ]),
            CreateAction::make(),
        ];
    }
}
