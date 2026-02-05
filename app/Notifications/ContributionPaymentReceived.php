<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionPaymentReceived extends Notification implements ShouldQueue
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
        return ['mail', WhatsAppChannel::class, 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sppg = $this->invoice->sppg;
        $lembaga = $sppg->lembagaPengusul;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Verifikasi Pembayaran Kontribusi')
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Pembayaran Kontribusi dari **{$lembaga->nama_lembaga}** ({$sppg->nama_sppg}) menunggu verifikasi.")
            ->line("**Invoice:** {$this->invoice->invoice_number}")
            ->line("**Jumlah:** {$formattedAmount}")
            ->action('Verifikasi Sekarang', url('/admin/invoices'))
            ->line('Mohon segera lakukan verifikasi pembayaran di panel admin.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $lembaga = $sppg->lembagaPengusul;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return \Filament\Notifications\Notification::make()
            ->title('Pembayaran Kontribusi Masuk')
            ->body("Invoice {$this->invoice->invoice_number} sebesar {$formattedAmount} dari {$lembaga->nama_lembaga}.")
            ->icon('heroicon-o-credit-card')
            ->info()
            ->actions([
                \Filament\Notifications\Actions\Action::make('verify')
                    ->button()
                    ->url('/admin/invoices'),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $lembaga = $sppg->lembagaPengusul;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        $template = \App\Models\NotificationTemplate::where('key', 'contribution_payment_received')->first();
        
        $defaultMessage = "Assalamualaikum {{name}},\n\n"
            . "Ada pembayaran Kontribusi masuk dari *{{institution_name}}* ({{sppg_name}}).\n\n"
            . "📄 *Invoice*: {{invoice_number}}\n"
            . "💰 *Jumlah*: {{amount}}\n\n"
            . "Mohon segera lakukan verifikasi pembayaran di panel admin.\n\n"
            . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{institution_name}}' => $lembaga->nama_lembaga,
            '{{sppg_name}}' => $sppg->nama_sppg,
            '{{invoice_number}}' => $this->invoice->invoice_number,
            '{{amount}}' => $formattedAmount,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
