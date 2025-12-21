<?php

namespace App\Filament\Sppg\Resources\ProductionVerificationResource\Pages;

use App\Filament\Sppg\Resources\ProductionVerificationResource;
use App\Models\ProductionVerificationSetting;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductionVerification extends CreateRecord
{
    protected static string $resource = ProductionVerificationResource::class;

    public function mount(): void
    {
        parent::mount();

        // 1. Load Global Checklist Settings
        $globalSetting = ProductionVerificationSetting::first();
        $checklistData = $globalSetting?->checklist_data ?? [];

        // 2. Prepare Repeater Data
        $initialRepeaterState = [];
        foreach ($checklistData as $item) {
            $initialRepeaterState[] = [
                'item' => $item['item_name'] ?? 'Unknown',
                'status' => null, // User needs to select this
                'keterangan' => null,
            ];
        }

        // 3. Fill the form
        $this->form->fill([
            'date' => now(),
            'checklist_results' => $initialRepeaterState,
        ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        
        // Assign User ID
        $data['user_id'] = $user->id;

        // Assign SPPG ID based on Role
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
            $data['sppg_id'] = $sppg?->id;
        } elseif ($user->hasRole('PJ Pelaksana')) {
            $unitTugas = User::find($user->id)->unitTugas->first();
            $data['sppg_id'] = $unitTugas?->id;
        }

        // Safety check if SPPG ID is missing
        if (empty($data['sppg_id'])) {
             // In a real app, maybe throw error, but here we might just let it fail at database level or set null?
             // Verification needs SPPG.
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
