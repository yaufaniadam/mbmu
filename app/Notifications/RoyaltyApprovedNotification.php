<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoyaltyApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        if ($notifiable->routeNotificationFor('WhatsApp')) {
            $channels[] = WhatsAppChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Pembayaran Kontribusi Diterima')
            ->body("Pembayaran Kontribusi #{$this->invoice->invoice_number} telah disetujui.")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->button()
                    ->url('/admin/manage-finance?activeTab=pay_royalty'),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $template = \App\Models\NotificationTemplate::where('key', 'royalty_approved')->first();

        // Fallback message
        $defaultMessage = "Assalamualaikum {{name}},\n\n"
                    . "✅ *PEMBAYARAN DITERIMA*\n\n"
                    . "Pembayaran Kontribusi Kornas untuk Invoice *{{invoice_number}}* telah diterima dan diverifikasi.\n\n"
                    . "Terima kasih atas kontribusi Anda untuk kemajuan bersama.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{invoice_number}}' => $this->invoice->invoice_number,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
