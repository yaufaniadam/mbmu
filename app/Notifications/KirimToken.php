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
        $message = "Assalamualaikum {$this->token->recipient_name},\n\n"
            . "Silakan login ke MBMu App lalu buat akun.\n\n"
            . "👉 Link: {$this->token->getRegistrationUrl()}\n"
            . "🔑 Token: {$this->token->token}\n\n"
            . "Gunakan link dan token di atas untuk mendaftar sebagai {$this->token->role_label} di {$this->token->sppg->nama_sppg}.\n\n"
            . "Terima Kasih.";

        return [
            'phone' => $notifiable->routeNotificationFor('WhatsApp'),
            'message' => $message
        ];
    }
}
