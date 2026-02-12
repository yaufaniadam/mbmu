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
     * Get the model related to this notification for polymorphic tracking.
     */
    public function getRelatedModel()
    {
        return $this->instruction;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // 'mail' removed due to invalid SMTP configuration blocking other channels
        return [WhatsAppChannel::class, 'database'];
    }

    /**
     * Get the appropriate URL for the notification based on user role.
     */
    protected function getNotificationUrl(object $notifiable): string
    {
        // List of roles that have access to the Admin panel
        // Copied from User::canAccessPanel('admin') logic
        $adminRoles = [
            'Superadmin', 
            'Direktur Kornas', 
            'Staf Kornas', 
            'Staf Akuntan Kornas', 
            'Pimpinan Lembaga Pengusul', 
            'PJ Pelaksana'
        ];

        if ($notifiable instanceof \App\Models\User && $notifiable->hasRole($adminRoles)) {
            return url('/admin/instructions/' . $this->instruction->id);
        }

        // Default to SPPG panel instruction list for others
        return url('/sppg/instruction-list');
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->getNotificationUrl($notifiable);

        $mailMessage = (new MailMessage)
            ->subject('Instruksi Baru: ' . $this->instruction->title)
            ->greeting("Assalamualaikum {$notifiable->name},")
            ->line('Ada instruksi baru yang perlu Anda baca:')
            ->line("**Judul:** {$this->instruction->title}")
            ->line("**Ringkasan:**")
            ->line(str($this->instruction->content)->limit(100))
            ->action('Baca Instruksi Lengkap', $url)
            ->line('Mohon segera membaca dan memahami instruksi tersebut.');

        if ($this->instruction->attachment_path) {
            $disk = \Illuminate\Support\Facades\Storage::disk('public');
            if ($disk->exists($this->instruction->attachment_path)) {
                $mailMessage->attach($disk->path($this->instruction->attachment_path));
            }
        }

        return $mailMessage;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        $url = $this->getNotificationUrl($notifiable);

        return \Filament\Notifications\Notification::make()
            ->title('Instruksi Baru')
            ->body("Instruksi baru: {$this->instruction->title}. Silakan cek sekarang.")
            ->icon('heroicon-o-megaphone')
            ->info()
            ->actions([
                \Filament\Actions\Action::make('read')
                    ->button()
                    ->url($url),
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
        $url = $this->getNotificationUrl($notifiable);

        $template = \App\Models\NotificationTemplate::where('key', 'instruction_published')->first();

        $defaultMessage = "Assalamualaikum {{name}},\n\n"
            . "📢 *INSTRUKSI BARU*\n\n"
            . "*{{title}}*\n\n"
            . "{{snippet}}\n\n"
            . "Silakan baca selengkapnya di aplikasi:\n"
            . "{{url}}\n\n";

        if ($this->instruction->attachment_path) {
            $attachmentUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($this->instruction->attachment_path);
            $defaultMessage .= "Unduh Lampiran:\n" . $attachmentUrl . "\n\n";
        }

        $defaultMessage .= "Terima kasih.";

        $messageContent = $template ? $template->content : $defaultMessage;

        // Ensure attachment URL is included if using a template that doesn't have the placeholder
        if ($template && $this->instruction->attachment_path && !str_contains($messageContent, '{{attachment_url}}')) {
             $messageContent .= "\n\nUnduh Lampiran:\n{{attachment_url}}";
        }
        
        $attachmentUrl = $this->instruction->attachment_path 
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->instruction->attachment_path) 
            : '-';

        $placeholders = [
            '{{name}}' => $notifiable->name,
            '{{title}}' => $title,
            '{{snippet}}' => $snippet,
            '{{url}}' => $url,
            '{{attachment_url}}' => $attachmentUrl,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        // Fallback: If using default message and attachment exists, ensure it's there (already done above in defaultMessage construction)
        // If using custom template and they didn't put {{attachment_url}}, it won't show.
        // We could force append it if not present, but that might break formatting.
        // For now, let's rely on the updated default message or the user updating their template.
        
        // However, since I modified $defaultMessage above, if $template is null, it works.
        // If $template is NOT null, I need to make sure I didn't break anything. 
        // The safe bet is to just pass the placeholders.

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message,
            'document' => $attachmentUrl !== '-' ? $attachmentUrl : null,
        ];
    }
}
