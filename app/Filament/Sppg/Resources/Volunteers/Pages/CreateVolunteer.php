<?php

namespace App\Filament\Sppg\Resources\Volunteers\Pages;

use App\Filament\Sppg\Resources\Volunteers\VolunteerResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateVolunteer extends CreateRecord
{
    protected static string $resource = VolunteerResource::class;

    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();

        $sppgId = $user->getManagedSppg();

        if (!$sppgId) {
            Notification::make()
                ->title('Anda belum ditugaskan ke sppg. Hubungi admin.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));

            return;
        }
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $sppg = $user->getManagedSppg();

        if (!$sppg) {
            Notification::make()
                ->title('Anda belum ditugaskan ke sppg. Hubungi admin.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'sppg' => 'Anda belum ditugaskan ke sppg. Hubungi admin.',
            ]);
        }

        return array_merge($data, ['sppg_id' => $sppg->id]);
    }
}
