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

        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "Invoice Baru telah diterbitkan untuk {$sppg->nama_sppg}.\n\n"
            . "📄 *Invoice*: {$this->invoice->invoice_number}\n"
            . "🗓 *Periode*: {$periode}\n"
            . "💰 *Total*: {$formattedAmount}\n\n"
            . "Mohon segera lakukan pembayaran ke rekening berikut:\n"
            . "🏦 *Bank*: {$bankName}\n"
            . "💳 *No. Rek*: {$accountNumber}\n"
            . "An. {$lembagaName}\n\n"
            . "Terima kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
