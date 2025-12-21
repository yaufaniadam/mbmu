<?php

namespace App\Filament\Resources\ProductionSchedules\Pages;

use App\Filament\Resources\ProductionSchedules\ProductionScheduleResource;
use App\Models\Distribution;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EditProductionSchedule extends EditRecord
{
    protected static string $resource = ProductionScheduleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // === NEW LOGIC TO FIX VALIDATION ===

        // 1. Get all schools for the current user (just like the form does)
        $user = Auth::user();
        if (!$user) {
            return $data;
        }

        // 1. Get the SPPG scope for the current user
        $sppg = null;
        if ($user->hasRole('Kepala SPPG')) {
            $sppg = $user->sppgDikepalai;
        } elseif ($user->hasAnyRole(['PJ Pelaksana', 'Staf Administrator SPPG', 'Ahli Gizi', 'Staf Gizi', 'Staf Akuntan', 'Staf Pengantaran'])) {
            $sppg = $user->unitTugas->first();
        }

        if (!$sppg) {
            return $data;
        }

        // Use the schools from this SPPG
        $schools = $sppg->schools;
        if ($schools->isEmpty()) {
            return $data;
        }

        // 2. Get existing distributions and key them by school_id for fast lookup
        $existingDistributions = $this->getRecord()
            ->distributions()
            ->get()
            ->keyBy('sekolah_id');

        // 3. Initialize the 'porsi_per_sekolah' array
        $data['porsi_per_sekolah'] = [];

        // 4. Loop through ALL schools, not just existing distributions
        foreach ($schools as $school) {
            $schoolId = $school->id;

            // Check if we have an existing distribution for this school
            $distribution = $existingDistributions->get($schoolId);

            // Fill data for this school, defaulting to 0 if no distribution exists
            $data['porsi_per_sekolah'][$schoolId] = [
                'sekolah_id' => $schoolId,
                'jumlah_porsi_besar' => $distribution ? $distribution->jumlah_porsi_besar : 0,
                'jumlah_porsi_kecil' => $distribution ? $distribution->jumlah_porsi_kecil : 0,
            ];
        }

        return $data; // Return the modified data
    }

    // ADDED THIS METHOD FOR EXTRA TESTING
    // afterSave runs for both Create and Update. Let's see if this one fires.
    protected function afterSave(): void
    {
        // MOVED LOGIC FROM afterUpdate() HERE
        Log::info('--- EditProductionSchedule: afterSave() IS BEING CALLED. ---');

        // --- THIS IS THE FIX ---
        // Use getState() to get ALL form data, not just dirty data
        $data = $this->form->getState();
        // --- END OF FIX ---

        // ADDED LOGGING
        Log::info('Data from getState(): ' . json_encode($data));

        $record = $this->getRecord(); // Get the ProductionSchedule that was updated

        // 1. Delete all existing distributions for this schedule
        $record->distributions()->delete();
        Log::info('Deleted distributions for record: ' . $record->id); // Log deletion

        // 2. Re-create the distributions based on the new form data
        if (isset($data['porsi_per_sekolah']) && is_array($data['porsi_per_sekolah'])) {
            $distributions = [];
            foreach ($data['porsi_per_sekolah'] as $porsi) { // No need for the $sekolahId key here

                // Ensure the 'sekolah_id' from the hidden field exists
                if (!isset($porsi['sekolah_id'])) {
                    Log::warning('Skipping distribution, missing sekolah_id: ' . json_encode($porsi));
                    continue; // Skip if data is incomplete
                }

                $distributions[] = [
                    'jadwal_produksi_id' => $record->id,
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
                Log::info('Re-created ' . count($distributions) . ' distributions.'); // Log creation
            }
        }
    }
}
