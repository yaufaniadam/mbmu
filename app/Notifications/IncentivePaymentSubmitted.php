<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IncentivePaymentSubmitted extends Notification implements ShouldQueue
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
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        
        return \Filament\Notifications\Notification::make()
            ->title('Pembayaran Insentif Masuk')
            ->body("Pembayaran {$formattedAmount} dari {$sppg->nama_sppg} menunggu verifikasi.")
            ->icon('heroicon-o-banknotes')
            ->success()
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->button()
                    ->url('/lembaga/manage-finance?activeTab=verify_rent'),
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
        
        $template = \App\Models\NotificationTemplate::where('key', 'sppg_incentive_payment')->first();

        // Fallback message
        $defaultMessage = "Assalamualaikum {{name}},\n\n"
                    . "Ada konfirmasi pembayaran Insentif dari *{{sppg_name}}*.\n\n"
                    . "📄 *Invoice*: {{invoice_number}}\n"
                    . "💰 *Jumlah*: {{amount}}\n"
                    . "🏦 *Bank*: {{source_bank}} ({{transfer_date}})\n\n"
                    . "Mohon segera verifikasi di Panel Admin Lembaga.\n\n"
                    . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{sppg_name}}' => $sppg->nama_sppg,
            '{{invoice_number}}' => $this->invoice->invoice_number,
            '{{amount}}' => $formattedAmount,
            '{{source_bank}}' => $this->invoice->source_bank ?? '-',
            '{{transfer_date}}' => $this->invoice->transfer_date ? $this->invoice->transfer_date->format('d M Y') : '-',
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
