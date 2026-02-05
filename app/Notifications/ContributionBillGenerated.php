<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionBillGenerated extends Notification implements ShouldQueue
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
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        $dueDate = $this->invoice->due_date->format('d M Y');

        return (new MailMessage)
            ->subject('Tagihan Kontribusi Kornas Baru')
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Tagihan Kontribusi Kornas untuk **{$sppg->nama_sppg}** telah terbit.")
            ->line("**Invoice:** {$this->invoice->invoice_number}")
            ->line("**Jumlah:** {$formattedAmount}")
            ->line("**Jatuh Tempo:** {$dueDate}")
            ->action('Lihat Tagihan', url('/admin/invoices'))
            ->line('Mohon segera lakukan pembayaran sebelum tanggal jatuh tempo.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return \Filament\Notifications\Notification::make()
            ->title('Tagihan Kontribusi Baru')
            ->body("Invoice {$this->invoice->invoice_number} sebesar {$formattedAmount} untuk {$sppg->nama_sppg}.")
            ->icon('heroicon-o-banknotes')
            ->warning()
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
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
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        $dueDate = $this->invoice->due_date->format('d M Y');

        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "Tagihan Kontribusi Kornas baru telah terbit untuk *{$sppg->nama_sppg}*.\n\n"
            . "📄 *Invoice*: {$this->invoice->invoice_number}\n"
            . "💰 *Jumlah*: {$formattedAmount}\n"
            . "🗓 *Jatuh Tempo*: {$dueDate}\n\n"
            . "Mohon segera lakukan pembayaran melalui aplikasi.\n\n"
            . "Terima kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
