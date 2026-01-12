<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $server;
    protected $token;
    protected $secretKey;

    /**
     * Mengambil kredensial dari file konfigurasi.
     */
    public function __construct()
    {
        $this->server = config('whatsapp.server');
        $this->token = config('whatsapp.token');
        $this->secretKey = config('whatsapp.secret_key'); // Tambahan dari contoh Wablas
    }

    /**
     * Fungsi utama untuk mengirim pesan.
     *
     * @param string $target Nomor HP tujuan (format internasional, e.g., 62812xxxx)
     * @param string $message Isi pesan yang akan dikirim
     * @return void
     */
    public function sendMessage($target, $message)
    {
        // Jika salah satu kredensial tidak ada, jangan lakukan apa-apa.
        // Ini berguna untuk mode development agar tidak error.
        if (!$this->server || !$this->token || !$this->secretKey) {
            Log::warning('WhatsApp Service: Kredensial API tidak lengkap. Pesan tidak dikirim.');
            return;
        }

        try {
            // Membangun URL sesuai format Wablas
            $fullUrl = $this->server . '/api/send-message';

            // Mengirim request menggunakan Laravel HTTP Client
            $response = Http::get($fullUrl, [
                'token' => $this->token . '.' . $this->secretKey, // Menggabungkan token dan secret key
                'phone' => $target,
                'message' => $message,
            ]);

            // (Opsional) Log respons dari API untuk debugging
            if ($response->failed()) {
                Log::error('WhatsApp Service Gagal:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } else {
                Log::info('WhatsApp Service Berhasil:', ['body' => $response->body()]);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception:', ['error' => $e->getMessage()]);
        }
    }
    
}