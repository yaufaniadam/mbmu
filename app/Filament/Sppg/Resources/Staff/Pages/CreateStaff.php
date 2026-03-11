<?php

namespace App\Filament\Sppg\Resources\Staff\Pages;

use App\Filament\Sppg\Resources\Staff\StaffResource;
use App\Models\SppgUserRole;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    public function mount(): void
    {
        $user = Auth::user();

        $sppg = $user->getManagedSppg();

        if (!$sppg) {
            Notification::make()
                ->title('Anda tidak memiliki akses ke halaman ini. Hubungi admin.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $user = Auth::user();

        $sppg = $user->getManagedSppg();

        if ($sppg) {
            // Get the roles that were just assigned to the user (via the form's relationship field)
            $roles = $record->roles; 

            foreach ($roles as $role) {
                // Check if this specific user-role-sppg combination already exists to avoid duplicates
                
                SppgUserRole::firstOrCreate([
                    'user_id' => $record->id,
                    'sppg_id' => $sppg->id,
                    'role_id' => $role->id,
                ]);
            }
        }
    }
}
