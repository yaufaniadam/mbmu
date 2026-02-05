<?php

namespace App\Filament\Admin\Resources\Instructions\Pages;

use App\Filament\Admin\Resources\Instructions\InstructionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInstruction extends CreateRecord
{
    protected static string $resource = InstructionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the creator to the current user
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        $instruction = $this->getRecord();
        $recipients = $instruction->getTargetedUsers();

        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new \App\Notifications\InstructionPublished($instruction));
            } catch (\Exception $e) {
                // Log error but continue
            }
        }
        
        \Filament\Notifications\Notification::make()
            ->title('Instruksi diterbitkan dan notifikasi dikirim ke ' . $recipients->count() . ' pengguna')
            ->success()
            ->send();
    }
}
