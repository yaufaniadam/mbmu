<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Complaint;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $complaint;

    /**
     * Create a new notification instance.
     */
    public function __construct(Complaint $complaint)
    {
        $this->complaint = $complaint;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // $channels = ['mail', 'database'];
        $channels = ['database'];
        
        if ($notifiable->routeNotificationFor('WhatsApp')) {
            $channels[] = WhatsAppChannel::class;
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $sender = $this->complaint->user->name;
        $subject = $this->complaint->subject;

        return (new MailMessage)
            ->subject('Pengaduan Baru Masuk')
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line("Ada pengaduan baru dari **{$sender}**.")
            ->line("**Subjek:** {$subject}")
            ->line("**Isi:**")
            ->line(str($this->complaint->content)->limit(150))
            ->action('Lihat Pengaduan', url('/admin/complaints/' . $this->complaint->id))
            ->line('Mohon segera ditindaklanjuti.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $sender = $this->complaint->user->name;
        
        return \Filament\Notifications\Notification::make()
            ->title('Pengaduan Baru')
            ->body("Pengaduan dari {$sender}: {$this->complaint->subject}")
            ->icon('heroicon-o-chat-bubble-left-right')
            ->warning()
            ->actions([
                \Filament\Actions\Action::make('view')
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
        $sender = $this->complaint->user->name;
        $subject = $this->complaint->subject;
        $snippet = str($this->complaint->content)->limit(100);

        $message = "Assalamualaikum {$notifiable->name},\n\n"
            . "🔔 *PENGADUAN BARU*\n\n"
            . "Dari: *{$sender}*\n"
            . "Subjek: *{$subject}*\n\n"
            . "{$snippet}\n\n"
            . "Mohon segera cek di panel admin.\n\n"
            . "Terima kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
