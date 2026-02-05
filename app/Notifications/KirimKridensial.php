<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;

class KirimKridensial extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
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
        $template = \App\Models\NotificationTemplate::where('key', 'credential_pimpinan')->first();
        $messageContent = $template ? $template->content : "Assalamualaikum {{name}},\n\nBerikut akses login Anda: {{username}} / {{password}}";

        $placeholders = [
            '{{name}}' => $this->user->name,
            '{{username}}' => $this->user->telepon,
            '{{password}}' => $this->password,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        $phone = null;
        // Check if notification is routed manually
        if (method_exists($notifiable, 'routeNotificationFor')) {
            $phone = $notifiable->routeNotificationFor('WhatsApp');
            \Illuminate\Support\Facades\Log::info('KirimKridensial: Routed Phone', ['phone' => $phone]);
        }

        // Fallback to user's phone if no route provided
        if (!$phone && !empty($this->user->telepon)) {
            $phone = $this->user->telepon;
            \Illuminate\Support\Facades\Log::info('KirimKridensial: User Telepon', ['phone' => $phone]);
        }

        // Check legacy property just in case
        if (!$phone && !empty($this->user->phone_number)) {
             $phone = $this->user->phone_number;
             \Illuminate\Support\Facades\Log::info('KirimKridensial: User PhoneNumber', ['phone' => $phone]);
        }

        \Illuminate\Support\Facades\Log::info('KirimKridensial: Final Phone', ['phone' => $phone, 'user_id' => $this->user->id ?? 'unknown']);

        return [
            'phone' => $phone, 
            'message' => $message
        ];
    }
}
