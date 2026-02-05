<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintResponded extends Notification implements ShouldQueue
{
    use Queueable;

    public $complaint;
    public $responseMessage;

    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint, string $responseMessage)
    {
        $this->complaint = $complaint;
        $this->responseMessage = $responseMessage;
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
        return (new MailMessage)
            ->subject('Pengaduan Anda Telah Direspon')
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Pengaduan Anda dengan subjek **{$this->complaint->subject}** telah mendapatkan respon.")
            ->line("**Respon:**")
            ->line(str($this->responseMessage)->limit(200))
            ->action('Lihat Detail', url('/admin/complaints/' . $this->complaint->id))
            ->line('Terima kasih.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Respon Pengaduan')
            ->body("Pengaduan '{$this->complaint->subject}' telah direspon. Cek sekarang.")
            ->icon('heroicon-o-check-circle')
            ->success()
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->button()
                    ->url('/admin/complaints/' . $this->complaint->id),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $subject = $this->complaint->subject;
        $snippet = str($this->responseMessage)->limit(100);

        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "📩 *RESPON PENGADUAN*\n\n"
            . "Pengaduan Anda tentang *{$subject}* telah direspon:\n\n"
            . "_{$snippet}_\n\n"
            . "Silakan cek detailnya di aplikasi.\n\n"
            . "Terima kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
