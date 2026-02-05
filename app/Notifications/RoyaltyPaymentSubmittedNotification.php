<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoyaltyPaymentSubmittedNotification extends Notification implements ShouldQueue
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
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        $sppg = $this->invoice->sppg;
        $lpName = $sppg->lembagaPengusul->nama_lembaga ?? $sppg->nama_sppg;

        return \Filament\Notifications\Notification::make()
            ->title('Pembayaran Kontribusi Masuk')
            ->body("Pembayaran {$formattedAmount} dari {$lpName} menunggu verifikasi.")
            ->icon('heroicon-o-currency-dollar')
            ->success()
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->button()
                    ->url('/admin/manage-finance?activeTab=verify_royalty'),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $template = \App\Models\NotificationTemplate::where('key', 'royalty_payment_submitted')->first();
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        $sppg = $this->invoice->sppg;
        $lpName = $sppg->lembagaPengusul->nama_lembaga ?? $sppg->nama_sppg;

        // Fallback message
        $defaultMessage = "Assalamualaikum Admin Kornas,\n\n"
                    . "💰 *PEMBAYARAN KONTRIBUSI*\n\n"
                    . "Ada pembayaran kontribusi masuk dari *{{lp_name}}*.\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🏦 *Bank*: {{source_bank}}\n\n"
                    . "Mohon segera verifikasi di Panel Admin Kornas.\n\n"
                    . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{lp_name}}' => $lpName,
            '{{invoice_number}}' => $this->invoice->invoice_number,
            '{{amount}}' => $formattedAmount,
            '{{source_bank}}' => $this->invoice->source_bank ?? '-',
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
