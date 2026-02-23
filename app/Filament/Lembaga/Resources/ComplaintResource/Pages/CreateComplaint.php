<?php

namespace App\Filament\Lembaga\Resources\ComplaintResource\Pages;

use App\Filament\Lembaga\Resources\ComplaintResource;
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

    protected function afterCreate(): void
    {
        $complaint = $this->getRecord();
        
        // Notify Admins and Kornas Staff
        $recipients = \App\Models\User::role(['Superadmin', 'Staf Kornas', 'Ketua Kornas'])->get();
        
        foreach ($recipients as $recipient) {
            try {
                \Illuminate\Support\Facades\Log::info("Sending complaint notification to: " . $recipient->name);
                $recipient->notify(new \App\Notifications\ComplaintSubmitted($complaint));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to notify " . $recipient->name . ": " . $e->getMessage());
            }
        }
    }
}
