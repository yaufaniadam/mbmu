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

        $service = new WhatsAppService();

        // Tentukan model terkait (default: notifiable user)
        $relatedModel = $notifiable;
        if (method_exists($notification, 'getRelatedModel')) {
            $relatedModel = $notification->getRelatedModel();
        }

        // Cek jika ada dokumen yang akan dikirim
        if (isset($data['document']) && !empty($data['document'])) {
            $service->sendDocument($targetNumber, $data['document'], $message, $relatedModel);
        } else {
            // Kirim pesan biasa
            $service->sendMessage($targetNumber, $message, $relatedModel);
        }
    }
}
