<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use App\Models\ComplaintMessage;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;

    public ?string $replyMessage = '';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('close')
                ->label('Tutup Tiket')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status !== 'Closed')
                ->action(function () {
                    $this->record->update(['status' => 'Closed']);
                    Notification::make()->title('Tiket ditutup')->success()->send();
                }),
            Action::make('reopen')
                ->label('Buka Kembali')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn () => $this->record->status === 'Closed' && Auth::user()->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas']))
                ->action(function () {
                    $this->record->update(['status' => 'Open']);
                    Notification::make()->title('Tiket dibuka kembali')->success()->send();
                }),
        ];
    }

    public function sendReply(): void
    {
        if (empty(trim($this->replyMessage ?? ''))) {
            Notification::make()->title('Pesan tidak boleh kosong')->danger()->send();
            return;
        }

        ComplaintMessage::create([
            'complaint_id' => $this->record->id,
            'user_id' => Auth::id(),
            'message' => $this->replyMessage,
        ]);

        // Update complaint status
        $user = Auth::user();
        if ($user->hasAnyRole(['Superadmin', 'Direktur Kornas', 'Staf Akuntan Kornas', 'Staf Kornas'])) {
            $this->record->update([
                'status' => 'Responded',
                'feedback_by' => Auth::id(),
                'feedback_at' => now(),
            ]);
        } else {
            if ($this->record->status === 'Responded') {
                $this->record->update(['status' => 'Open']);
            }
        }

        $this->record->refresh();
        $this->replyMessage = '';
        
        Notification::make()->title('Pesan terkirim')->success()->send();
    }

    protected function getViewData(): array
    {
        return [
            'record' => $this->record,
        ];
    }

    public function getView(): string
    {
        return 'filament.resources.complaint-resource.pages.view-complaint';
    }
}
