<?php

namespace App\Filament\Sppg\Resources\InvoiceResource\Pages;

use App\Filament\Sppg\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Automatically change status to WAITING_VERIFICATION whenever SPPG saves the form (uploads proof)
        // Check if verification is not already paid
        $record = $this->getRecord();
        if ($record->status !== 'PAID') {
            $data['status'] = 'WAITING_VERIFICATION';
        }
        
        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
