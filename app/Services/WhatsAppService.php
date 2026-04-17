<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send message via the configured gateway.
     *
     * @param string $to
     * @param string $message
     * @return array
     */
    public function send(string $to, string $message): array
    {
        $gateway = config('whatsapp.gateway', 'manual');

        switch ($gateway) {
            case 'fonnte':
                return $this->sendViaFonnte($to, $message);
            case 'wablas':
                return $this->sendViaWablas($to, $message);
            default:
                return [
                    'success' => false,
                    'message' => 'Manual gateway requires front-end redirection.',
                ];
        }
    }

    /**
     * Get the manual redirection URL.
     *
     * @param string $to
     * @param string $message
     * @return string
     */
    public function getManualUrl(string $to, string $message): string
    {
        $encodedMessage = rawurlencode($message);
        return "https://api.whatsapp.com/send?phone={$to}&text={$encodedMessage}";
    }

    /**
     * Send message via Fonnte API.
     */
    protected function sendViaFonnte(string $to, string $message): array
    {
        $token = config('whatsapp.fonnte.token');

        if (!$token) {
            return ['success' => false, 'message' => 'Fonnte Token is missing in .env'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                return ['success' => true, 'message' => 'Message sent via Fonnte.'];
            }

            return ['success' => false, 'message' => $result['reason'] ?? 'Fonnte API error.'];
        } catch (\Exception $e) {
            Log::error('Fonnte Send Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Send message via Wablas API.
     */
    protected function sendViaWablas(string $to, string $message): array
    {
        $server = config('whatsapp.wablas.server');
        $token = config('whatsapp.wablas.token');

        if (!$server || !$token) {
            return ['success' => false, 'message' => 'Wablas configuration is missing in .env'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->get("{$server}/api/send-message", [
                'phone' => $to,
                'message' => $message,
            ]);

            $result = $response->json();

            if ($response->successful() && ($result['status'] ?? false)) {
                return ['success' => true, 'message' => 'Message sent via Wablas.'];
            }

            return ['success' => false, 'message' => $result['message'] ?? 'Wablas API error.'];
        } catch (\Exception $e) {
            Log::error('Wablas Send Error: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}