<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WablasChannel;

class WaResetPassword extends Notification
{
    use Queueable;

    protected string $url;
    protected string $name;

    public function __construct(string $url, string $name)
    {
        $this->url = $url;
        $this->name = $name;
    }

    public function via($notifiable): array
    {
        return [WablasChannel::class];
    }

    public function toWhatsApp($notifiable)
    {
        $message = "*Permintaan Reset Password*\n\n" .
                  "Halo, {$this->name}\n\n" .
                  "Kami menerima permintaan untuk mereset password akun MBM-U Anda. Silakan klik link di bawah ini untuk mengatur password baru:\n\n" .
                  "Link Reset:\n" .
                  "{$this->url}\n\n" .
                  "Link ini hanya berlaku selama 60 menit. Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan pesan ini.\n\n" .
                  "---\n" .
                  "*Tim MBM-U*";

        return [
            'message' => $message,
        ];
    }
}
