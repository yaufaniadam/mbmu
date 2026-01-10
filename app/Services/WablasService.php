<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasService
{
    protected string $apiUrl = 'https://pati.wablas.com/api/send-message';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.wablas.token');
    }

    /**
     * Send a WhatsApp message
     */
    public function sendMessage(string $phone, string $message): bool
    {
        if (!$this->token) {
            Log::warning('Wablas token not configured, skipping WA notification');
            return false;
        }

        // Normalize phone number (remove leading 0, add 62)
        $phone = $this->normalizePhone($phone);

        try {
            $response = Http::withToken($this->token)
                ->post($this->apiUrl, [
                    'phone' => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info("WA message sent to {$phone}");
                return true;
            }

            Log::error("Failed to send WA message to {$phone}", [
                'response' => $response->json(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("WA service error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Normalize Indonesian phone number to international format
     */
    protected function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading 0 and add 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        // Add 62 if not present
        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send registration success notification
     */
    public function sendRegistrationSuccess(string $phone, string $name, string $password, string $sppgName, string $role): bool
    {
        $message = "🎉 *Selamat, {$name}!*\n\n";
        $message .= "Akun MBM Anda berhasil dibuat.\n\n";
        $message .= "📍 *SPPG:* {$sppgName}\n";
        $message .= "👤 *Jabatan:* {$role}\n\n";
        $message .= "🔐 *Login:* {$phone}\n";
        $message .= "🔑 *Password:* {$password}\n\n";
        $message .= "🌐 Silakan login di:\n";
        $message .= config('app.url') . "/sppg\n\n";
        $message .= "⚠️ _Jangan bagikan password Anda._";

        return $this->sendMessage($phone, $message);
    }
}
