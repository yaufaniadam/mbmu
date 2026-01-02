<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Pages;

use App\Filament\Resources\SppgFinancialReportResource;
use App\Models\Sppg;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSppgFinancialReport extends CreateRecord
{
    protected static string $resource = SppgFinancialReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        
        // Find SPPG for this user (Kepala SPPG)
        // Adjust logic if user has multiple SPPGs or different relation
        $sppg = Sppg::where('kepala_sppg_id', Auth::id())->first();
        if ($sppg) {
            $data['sppg_id'] = $sppg->id;
        } else {
             // Fallback or error? For now assume user IS linked.
             // If Testing as Admin, this might fail if Admin is not Kepala.
             // Admin might need to select SPPG?
             // But for now feature is for SPPG to upload.
        }

        return $data;
    }
}
