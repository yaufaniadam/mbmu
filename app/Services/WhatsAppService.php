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
     * @param mixed $relatedModel Model terkait untuk polymorphic relation (opsional)
     * @return void
     */
    public function sendMessage($target, $message, $relatedModel = null)
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

                // Simpan data pesan ke database
                $body = $response->json();
                $messageId = $body['data']['messages'][0]['id'] ?? null; // Sesuaikan dengan struktur respons Wablas
                $status = $body['status'] ?? 'pending';

                // Fallback jika struktur berbeda (kadang Wablas langsung return id di root atau data)
                // Asumsi: $body['data']['messages'][0]['id'] adalah format umum untuk bulk/single send
                
                if (!$messageId && isset($body['data']['id'])) {
                    $messageId = $body['data']['id'];
                }

                \App\Models\WhatsAppMessage::create([
                    'wablas_message_id' => $messageId,
                    'phone' => $target,
                    'message' => $message,
                    'status' => $status ? 'pending' : 'failed', // Default ke pending jika sukses terkirim ke Wablas
                    'related_type' => $relatedModel ? get_class($relatedModel) : null,
                    'related_id' => $relatedModel ? $relatedModel->id : null,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception:', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Mengirim dokumen/file via WhatsApp (Wablas).
     *
     * @param string $target Nomor HP tujuan
     * @param string $documentUrl URL publik file yang akan dikirim
     * @param string|null $caption Caption untuk file (opsional)
     * @param mixed $relatedModel Model terkait untuk polymorphic relation (opsional)
     * @return void
     */
    public function sendDocument($target, $documentUrl, $caption = '', $relatedModel = null)
    {
        if (!$this->server || !$this->token || !$this->secretKey) {
            Log::warning('WhatsApp Service: Kredensial API tidak lengkap. Dokumen tidak dikirim.');
            return;
        }

        try {
            $fullUrl = $this->server . '/api/send-document';

            // Authorization header format: "Authorization: token.secret_key"
            // Note: Laravel HttpClient handles headers slightly differently.
            // Based on user snippet:
            // curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: $token.$secret_key"));
            
            $headers = [
                'Authorization' => $this->token . '.' . $this->secretKey,
            ];

            $data = [
                'phone' => $target,
                'document' => $documentUrl,
                'caption' => $caption,
            ];

            $response = Http::withHeaders($headers)
                ->post($fullUrl, $data);

            if ($response->failed()) {
                Log::error('WhatsApp Service Document Gagal:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } else {
                Log::info('WhatsApp Service Document Berhasil:', ['body' => $response->body()]);

                $body = $response->json();
                $messageId = $body['data']['messages'][0]['id'] ?? null;
                
                if (!$messageId && isset($body['data']['id'])) {
                     $messageId = $body['data']['id'];
                }

                \App\Models\WhatsAppMessage::create([
                    'wablas_message_id' => $messageId,
                    'phone' => $target,
                    'message' => $caption,
                    'attachment_url' => $documentUrl,
                    'status' => 'pending',
                    'related_type' => $relatedModel ? get_class($relatedModel) : null,
                    'related_id' => $relatedModel ? $relatedModel->id : null,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('WhatsApp Service Exception (Document):', ['error' => $e->getMessage()]);
        }
    }
    
}