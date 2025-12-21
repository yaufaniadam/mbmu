<?php

namespace App\Filament\Admin\Resources\LembagaPengusuls\Pages;

use App\Filament\Admin\Resources\LembagaPengusuls\LembagaPengusulResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLembagaPengusul extends ViewRecord
{
    protected static string $resource = LembagaPengusulResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn () => auth()->user()->hasRole('Superadmin')),
        ];
    }
}
