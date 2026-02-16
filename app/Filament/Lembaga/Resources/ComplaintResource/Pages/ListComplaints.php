<?php

namespace App\Filament\Lembaga\Resources\ComplaintResource\Pages;

use App\Filament\Lembaga\Resources\ComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaints extends ListRecords
{
    protected static string $resource = ComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => \Illuminate\Support\Facades\Auth::user()->hasAnyRole(['Pimpinan Lembaga Pengusul', 'Kepala SPPG'])),
        ];
    }
}
