<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class WablasService
{
    protected $whatsAppService;

    public function __construct()
    {
        $this->whatsAppService = new WhatsAppService();
    }

    public function sendRegistrationSuccess($phone, $name, $password, $sppgName, $role)
    {
        $message = "Halo, {$name}!\n\n";
        $message .= "Selamat! Pendaftaran akun Anda sebagai *{$role}* di *{$sppgName}* telah berhasil.\n\n";
        $message .= "Berikut adalah detail akun Anda:\n";
        $message .= "Password: {$password}\n\n";
        $message .= "Silakan login menggunakan nomor HP Anda dan password di atas.\n";
        $message .= "Terima kasih.";

        try {
            $this->whatsAppService->sendMessage($phone, $message);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim pesan registrasi sukses: ' . $e->getMessage());
            // We don't throw here to avoid breaking the registration flow
        }
    }
}
