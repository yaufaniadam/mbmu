<?php

namespace App\Filament\Sppg\Resources\ProductionSchedules\Pages;

use App\Filament\Sppg\Resources\ProductionSchedules\ProductionScheduleResource;
use App\Models\Distribution;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProductionSchedule extends CreateRecord
{
    protected static string $resource = ProductionScheduleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $sppgId = User::find($user->id)->unitTugas()->first()->id;

        $data['sppg_id'] = $sppgId;
        return $data;
    }

    protected function afterCreate(): void
    {
        $data = $this->data; // Get all form data
        $record = $this->getRecord(); // Get the ProductionSchedule that was just created

        if (isset($data['porsi_per_sekolah']) && is_array($data['porsi_per_sekolah'])) {
            $distributions = [];
            foreach ($data['porsi_per_sekolah'] as $sekolahId => $porsi) {

                // Ensure the 'sekolah_id' from the hidden field exists
                if (!isset($porsi['sekolah_id'])) {
                    continue; // Skip if data is incomplete
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
            if (!empty($distributions)) {
                Distribution::insert($distributions);
            }
        }
    }
}
