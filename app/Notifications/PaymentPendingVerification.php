<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentPendingVerification extends Notification implements ShouldQueue
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
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return (new MailMessage)
            ->subject('Pembayaran Insentif Menunggu Verifikasi')
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Ada pembayaran insentif dari **{$sppg->nama_sppg}** yang perlu diverifikasi.")
            ->line("**Invoice:** {$this->invoice->invoice_number}")
            ->line("**Jumlah:** {$formattedAmount}")
            ->action('Verifikasi Sekarang', url('/admin'))
            ->line('Mohon segera lakukan verifikasi pembayaran.');
    }

    /**
     * Get the database representation of the notification (Filament 4 format).
     */
    public function toDatabase(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return \Filament\Notifications\Notification::make()
            ->title('Pembayaran Menunggu Verifikasi')
            ->body("Invoice {$this->invoice->invoice_number} sebesar {$formattedAmount} dari {$sppg->nama_sppg}.")
            ->icon('heroicon-o-clock')
            ->warning()
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "Ada pembayaran insentif dari *{$sppg->nama_sppg}* yang perlu diverifikasi.\n\n"
            . "📄 *Invoice*: {$this->invoice->invoice_number}\n"
            . "💰 *Jumlah*: {$formattedAmount}\n\n"
            . "Mohon segera lakukan verifikasi pembayaran di panel admin.\n\n"
            . "Terima kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
