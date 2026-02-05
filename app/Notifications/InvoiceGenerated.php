<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InvoiceGenerated extends Notification implements ShouldQueue
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
        return [WhatsAppChannel::class, 'database'];
    }

    /**
     * Get the database representation of the notification (Filament 4 format).
     */
    public function toDatabase(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');

        return \Filament\Notifications\Notification::make()
            ->title('Invoice Baru Diterbitkan')
            ->body("Invoice {$this->invoice->invoice_number} sebesar {$formattedAmount} untuk {$sppg->nama_sppg}.")
            ->icon('heroicon-o-document-currency-dollar')
            ->warning()
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
        $periode = $this->invoice->start_date->format('d M Y') . ' - ' . $this->invoice->end_date->format('d M Y');
        
        $bankName = $lembaga?->nama_bank ?? '(Bank Belum Diatur)';
        $accountNumber = $lembaga?->nomor_rekening ?? '(Rekening Belum Diatur)';
        $lembagaName = $lembaga?->nama_lembaga ?? 'Lembaga Pengusul';

        $template = \App\Models\NotificationTemplate::where('key', 'invoice_generated')->first();

        $defaultMessage = "Assalamualaikum {{name}},\n\n"
            . "Invoice Baru telah diterbitkan untuk {{sppg_name}}.\n\n"
            . "📄 *Invoice*: {{invoice_number}}\n"
            . "🗓 *Periode*: {{period}}\n"
            . "💰 *Total*: {{amount}}\n\n"
            . "Mohon segera lakukan pembayaran ke rekening berikut:\n"
            . "🏦 *Bank*: {{bank_name}}\n"
            . "💳 *No. Rek*: {{account_number}}\n"
            . "An. {{account_holder}}\n\n"
            . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{sppg_name}}' => $sppg->nama_sppg,
            '{{invoice_number}}' => $this->invoice->invoice_number,
            '{{period}}' => $periode,
            '{{amount}}' => $formattedAmount,
            '{{bank_name}}' => $bankName,
            '{{account_number}}' => $accountNumber,
            '{{account_holder}}' => $lembagaName,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
