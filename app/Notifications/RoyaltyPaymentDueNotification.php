<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RoyaltyPaymentDueNotification extends Notification implements ShouldQueue
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

        return \Filament\Notifications\Notification::make()
            ->title('Tagihan Kontribusi Terbit')
            ->body("Tagihan Kontribusi (10%) sebesar {$formattedAmount} telah diterbitkan. Mohon segera dibayar.")
            ->icon('heroicon-o-banknotes')
            ->warning()
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->button()
                    ->url('/lembaga/manage-finance?activeTab=pay_royalty'),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $template = \App\Models\NotificationTemplate::where('key', 'royalty_payment_due')->first();
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        $dueDate = $this->invoice->due_date ? $this->invoice->due_date->format('d M Y') : '-';

        // Fallback message
        $defaultMessage = "Assalamualaikum {{name}},\n\n"
                    . "📢 *TAGIHAN KONTRIBUSI*\n\n"
                    . "Tagihan Kontribusi 10% untuk periode ini telah terbit secara otomatis.\n\n"
                    . "📄 *No Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🗓️ *Jatuh Tempo*: {{due_date}}\n\n"
                    . "Mohon segera lakukan pembayaran melalui menu *Bayar Kontribusi* di aplikasi.\n\n"
                    . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{invoice_number}}' => $this->invoice->invoice_number,
            '{{amount}}' => $formattedAmount,
            '{{due_date}}' => $dueDate,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
