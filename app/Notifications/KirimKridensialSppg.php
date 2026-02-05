<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;

class KirimKridensialSppg extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;
    public $password;

    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function via(object $notifiable): array
    {
        return [WhatsAppChannel::class];
    }

    public function toWhatsApp(object $notifiable): array
    {
        $template = \App\Models\NotificationTemplate::where('key', 'credential_sppg')->first();
        $messageContent = $template ? $template->content : "Assalamualaikum {{name}},\n\nBerikut akses login Anda: {{username}} / {{password}}";

        $placeholders = [
            '{{name}}' => $this->user->name,
            '{{username}}' => $this->user->telepon,
            '{{password}}' => $this->password,
        ];

        $message = str_replace(array_keys($placeholders), array_values($placeholders), $messageContent);

        $phone = null;
        if (method_exists($notifiable, 'routeNotificationFor')) {
            $phone = $notifiable->routeNotificationFor('WhatsApp');
            \Illuminate\Support\Facades\Log::info('KirimKridensialSppg: Routed Phone', ['phone' => $phone]);
        }

        if (!$phone && !empty($this->user->telepon)) {
            $phone = $this->user->telepon;
             \Illuminate\Support\Facades\Log::info('KirimKridensialSppg: User Telepon', ['phone' => $phone]);
        }

        // Fallback/Legacy
        if (!$phone && !empty($this->user->phone_number)) {
             $phone = $this->user->phone_number;
             \Illuminate\Support\Facades\Log::info('KirimKridensialSppg: User PhoneNumber', ['phone' => $phone]);
        }
        
        \Illuminate\Support\Facades\Log::info('KirimKridensialSppg: Final to', ['phone' => $phone]);

        return [
            'phone' => $phone, 
            'message' => $message
        ];
    }
}
