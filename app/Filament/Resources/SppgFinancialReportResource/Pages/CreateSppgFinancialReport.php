<?php

namespace App\Filament\Resources\SppgFinancialReportResource\Pages;

use App\Filament\Resources\SppgFinancialReportResource;
use App\Models\Sppg;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateSppgFinancialReport extends CreateRecord
{
    protected static string $resource = SppgFinancialReportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['user_id'] = $user->id;

        $sppgId = null;

        // 1. Kepala SPPG
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
            if ($sppg) {
                $sppgId = $sppg->id;
            }
        }

        // 2. Staff roles (Staf Akuntan, etc.)
        if (!$sppgId && $user->hasAnyRole(['Staf Akuntan', 'Admin SPPG', 'PJ Pelaksana', 'Staf Administrator SPPG'])) {
            $unitTugas = $user->unitTugas->first();
            if ($unitTugas) {
                $sppgId = $unitTugas->id;
            }
        }

        if ($sppgId) {
            $data['sppg_id'] = $sppgId;
        } else {
            Notification::make()
                ->title('Anda tidak terhubung dengan SPPG manapun.')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
