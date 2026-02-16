<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillDueReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public $invoice;
    public $status; // 'reminder' (H-3) or 'overdue'

    /**
     * Create a new notification instance.
     */
    public function __construct(Invoice $invoice, string $status = 'reminder')
    {
        $this->invoice = $invoice;
        $this->status = $status;
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
        
        $isOverdue = $this->status === 'overdue';
        $subject = $isOverdue ? 'Peringatan: Tagihan Jatuh Tempo' : 'Pengingat Pembayaran Tagihan';
        $intro = $isOverdue 
            ? "Tagihan untuk **{$sppg->nama_sppg}** telah melewati tanggal jatuh tempo." 
            : "Tagihan untuk **{$sppg->nama_sppg}** akan segera jatuh tempo.";

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line($intro)
            ->line("**Invoice:** {$this->invoice->invoice_number}")
            ->line("**Jumlah:** {$formattedAmount}")
            ->line("**Jatuh Tempo:** {$dueDate}")
            ->action('Bayar Sekarang', url($this->invoice->type === 'LP_ROYALTY' ? '/lembaga/manage-finance?activeTab=pay_royalty' : '/sppg/manage-finance?activeTab=pay_rent'))
            ->line('Mohon segera menyelesaikan pembayaran.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $sppg = $this->invoice->sppg;
        $formattedAmount = 'Rp ' . number_format($this->invoice->amount, 0, ',', '.');
        
        $isOverdue = $this->status === 'overdue';
        $title = $isOverdue ? 'Tagihan Lewat Jatuh Tempo' : 'Pengingat Tagihan';
        $body = $isOverdue
            ? "Invoice {$this->invoice->invoice_number} ({$sppg->nama_sppg}) telah melewati jatuh tempo."
            : "Invoice {$this->invoice->invoice_number} ({$sppg->nama_sppg}) akan jatuh tempo pada {$this->invoice->due_date->format('d M Y')}.";

        return \Filament\Notifications\Notification::make()
            ->title($title)
            ->body($body)
            ->icon($isOverdue ? 'heroicon-o-exclamation-circle' : 'heroicon-o-clock')
            ->color($isOverdue ? 'danger' : 'warning')
            ->actions([
                \Filament\Notifications\Actions\Action::make('pay')
                    ->button()
                    ->url($this->invoice->type === 'LP_ROYALTY' ? '/lembaga/manage-finance?activeTab=pay_royalty' : '/sppg/manage-finance?activeTab=pay_rent'),
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

        $isOverdue = $this->status === 'overdue';
        $header = $isOverdue ? "Peringatan Tagihan Jatuh Tempo" : "Pengingat Pembayaran";
        
        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "*{$header}*\n\n"
            . "Tagihan untuk *{$sppg->nama_sppg}* perlu perhatian Anda.\n\n"
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
