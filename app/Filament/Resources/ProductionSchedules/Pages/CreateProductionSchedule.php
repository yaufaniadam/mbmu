<?php

namespace App\Filament\Resources\ProductionSchedules\Pages;

use App\Filament\Resources\ProductionSchedules\ProductionScheduleResource;
use App\Models\Distribution;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateProductionSchedule extends CreateRecord
{
    protected static string $resource = ProductionScheduleResource::class;

    public function mount(): void
    {
        $user = Auth::user();

        $sppg = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        }

        if ($user->hasRole('PJ Pelaksana')) {
            $sppg = User::find($user->id)->unitTugas->first();
        }

        if (! $sppg) {
            Notification::make()
                ->title('Akun Anda belum terhubung dengan SPPG manapun. Silakan hubungi admin.')
                ->danger()
                ->send();

            $this->redirect($this->getResource()::getUrl('index'));
        }

        parent::mount();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $sppg = null;

        if ($user->hasRole('Kepala SPPG')) {
            $sppg = User::find($user->id)->sppgDikepalai;
        }

        if ($user->hasRole('PJ Pelaksana')) {
            $sppg = User::find($user->id)->unitTugas->first();
        }

        if (! $sppg) {
            Notification::make()
                ->title('Anda belum ditugaskan ke sppg. Hubungi admin.')
                ->danger()
                ->send();

            throw ValidationException::withMessages([
                'sppg' => 'Anda belum ditugaskan ke sppg. Hubungi admin.',
            ]);
        }

        $data['sppg_id'] = $sppg->id;
        
        // Calculate total portions
        $totalPortions = 0;
        if (isset($data['porsi_per_sekolah']) && is_array($data['porsi_per_sekolah'])) {
            foreach ($data['porsi_per_sekolah'] as $porsi) {
                $totalPortions += (int) ($porsi['jumlah_porsi_besar'] ?? 0);
                $totalPortions += (int) ($porsi['jumlah_porsi_kecil'] ?? 0);
            }
        }
        $data['jumlah'] = $totalPortions;

        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->data; // Get all form data
        $record = $this->getRecord(); // Get the ProductionSchedule that was just created

        // Get valid school IDs for this SPPG
        $validSchoolIds = \App\Models\School::where('sppg_id', $record->sppg_id)->pluck('id')->toArray();

        if (isset($data['porsi_per_sekolah']) && is_array($data['porsi_per_sekolah'])) {
            $distributions = [];
            foreach ($data['porsi_per_sekolah'] as $sekolahId => $porsi) {

                // Ensure the 'sekolah_id' from the hidden field exists
                if (! isset($porsi['sekolah_id'])) {
                    continue; // Skip if data is incomplete
                }

                // Security Check: Verify if school belongs to the SPPG
                if (!in_array($porsi['sekolah_id'], $validSchoolIds)) {
                    continue; // Skip unauthorized school
                }

                $distributions[] = [
                    'jadwal_produksi_id' => $record->id,
                    // Use the value from the hidden field
                    'sekolah_id' => $porsi['sekolah_id'],
                    'jumlah_porsi_besar' => $porsi['jumlah_porsi_besar'] ?? 0,
                    'jumlah_porsi_kecil' => $porsi['jumlah_porsi_kecil'] ?? 0,
                    'status_pengantaran' => 'Menunggu', // Set default
                    'created_at' => now(), // Manually set timestamps for mass insert
                    'updated_at' => now(),
                ];
            }

            // Use mass insert for better performance
            if (! empty($distributions)) {
                Distribution::insert($distributions);
            }
        }
    }
}
