<?php

namespace App\Filament\Lembaga\Resources\Invoices\Pages;

use App\Filament\Lembaga\Resources\Invoices\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('manual_generate')
                ->label('Manual Generate Tool')
                ->icon('heroicon-o-cog-6-tooth')
                ->url(InvoiceResource::getUrl('generate-tool')),
        ];
    }
}
