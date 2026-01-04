<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateComplaint extends CreateRecord
{
    protected static string $resource = ComplaintResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        
        // Auto-set source_type based on role
        if (Auth::user()->hasRole('Kepala SPPG')) {
            $data['source_type'] = 'sppg';
        } else {
            $data['source_type'] = 'lembaga_pengusul';
        }
        
        return $data;
    }
}
