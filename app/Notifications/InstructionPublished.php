<?php

namespace App\Notifications;

use App\Channels\WhatsAppChannel;
use App\Models\Instruction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructionPublished extends Notification implements ShouldQueue
{
    use Queueable;

    public $instruction;

    /**
     * Create a new notification instance.
     */
    public function __construct(Instruction $instruction)
    {
        $this->instruction = $instruction;
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
            ->subject('Instruksi Baru: ' . $this->instruction->title)
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line('Ada instruksi baru yang perlu Anda baca:')
            ->line("**Judul:** {$this->instruction->title}")
            ->line("**Ringkasan:**")
            ->line(str($this->instruction->content)->limit(100))
            ->action('Baca Instruksi Lengkap', url('/admin/instructions/' . $this->instruction->id))
            ->line('Mohon segera membaca dan memahami instruksi tersebut.');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return \Filament\Notifications\Notification::make()
            ->title('Instruksi Baru')
            ->body("Instruksi baru: {$this->instruction->title}. Silakan cek sekarang.")
            ->icon('heroicon-o-megaphone')
            ->info()
            ->actions([
                \Filament\Notifications\Actions\Action::make('read')
                    ->button()
                    ->url('/admin/instructions/' . $this->instruction->id),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the WhatsApp representation of the notification.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $title = $this->instruction->title;
        $snippet = str($this->instruction->content)->limit(100);

        $template = \App\Models\NotificationTemplate::where('key', 'instruction_published')->first();

        $defaultMessage = "Assalamualaikum {{name}},\n\n"
            . "📢 *INSTRUKSI BARU*\n\n"
            . "*{{title}}*\n\n"
            . "{{snippet}}\n\n"
            . "Silakan baca selengkapnya di aplikasi.\n\n"
            . "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{title}}' => $title,
            '{{snippet}}' => $snippet,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
