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

        // Cek jika ada dokumen/image yang akan dikirim
        if (isset($data['document']) && !empty($data['document'])) {
            // Detect file type by extension
            $fileExtension = strtolower(pathinfo($data['document'], PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($fileExtension, $imageExtensions)) {
                // Send as image (v2 API)
                $service->sendImage($targetNumber, $data['document'], $message, $relatedModel);
            } else {
                // Send as document (v2 API)
                $service->sendDocument($targetNumber, $data['document'], $message, $relatedModel);
            }
        } else {
            // Kirim pesan biasa
            $service->sendMessage($targetNumber, $message, $relatedModel);
        }
    }
}
