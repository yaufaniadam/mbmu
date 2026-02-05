<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;

class KirimToken extends Notification implements ShouldQueue
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Tentukan channel notifikasi yang akan digunakan.
     */
    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    /**
     * Mendefinisikan format pesan WhatsApp.
     */
    public function toWhatsApp(object $notifiable): array
    {
        $template = \App\Models\NotificationTemplate::where('key', 'registration_token')->first();
        $messageContent = $template ? $template->content : "Assalamualaikum {{recipient_name}},\n\nToken: {{token}}\nLink: {{registration_url}}";

        $placeholders = [
            '{{recipient_name}}' => $this->token->recipient_name,
            '{{registration_url}}' => $this->token->getRegistrationUrl(),
            '{{token}}' => $this->token->token,
            '{{role_label}}' => $this->token->role_label,
            '{{sppg_name}}' => $this->token->sppg->nama_sppg ?? '',
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
