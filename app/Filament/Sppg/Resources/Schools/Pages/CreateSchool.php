<?php

namespace App\Filament\Sppg\Resources\Schools\Pages;

use App\Filament\Sppg\Resources\Schools\SchoolResource;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateSchool extends CreateRecord
{
    protected static string $resource = SchoolResource::class;

    public function mount(): void
    {
        parent::mount();

        $user = Auth::user();

        $sppgId = User::find($user->id)->sppgDikepalai;

        if (!$sppgId) {
            Notification::make()
                ->title('Anda belum ditugaskan ke sppg. Hubungi admin.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));

            return;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $sppg = User::find($user->id)->sppgDikepalai;

        if (!$sppg) {
            Notification::make()
                ->title('Anda belum ditugaskan ke sppg. Hubungi admin.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'sppg' => 'Anda belum ditugaskan ke sppg. Hubungi admin.',
            ]);
        }

        $data['sppg_id'] = $sppg->id;
        return $data;
    }
}
