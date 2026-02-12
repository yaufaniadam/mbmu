<?php

namespace App\Filament\Admin\Resources\WhatsAppMessages\Pages;

use App\Filament\Admin\Resources\WhatsAppMessages\WhatsAppMessageResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use App\Services\WhatsAppService;
use Filament\Notifications\Notification;

class ViewWhatsAppMessage extends ViewRecord
{
    protected static string $resource = WhatsAppMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resend')
                ->label('Resend')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->requiresConfirmation()
                ->action(function (WhatsAppMessageResource $resource, $record, WhatsAppService $wa) {
                    // Note: In Page action, $record is available via $this->record or argument?
                    // Usually $record is available in closure if typehinted, or use $this->getRecord()
                    $record = $this->getRecord();
                    
                    if (!empty($record->attachment_url)) {
                        $wa->sendDocument($record->phone, $record->attachment_url, $record->message);
                    } else {
                        $wa->sendMessage($record->phone, $record->message);
                    }
                    
                    Notification::make()
                        ->title('Pesan dikirim ulang')
                        ->success()
                        ->send();
                }),
        ];
    }
}
