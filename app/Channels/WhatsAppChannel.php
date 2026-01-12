<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\WhatsAppService;

class WhatsAppChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toWhatsApp($notifiable);

        $targetNumber = $data['phone'];
        $message = $data['message'];

        // Kirim pesan menggunakan service yang sudah ada
        (new WhatsAppService())->sendMessage($targetNumber, $message);
    }
}
