<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\WaResetPassword;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

class ForgotPassword extends Component
{
    public string $telepon = '';
    public bool $success = false;
    public string $error = '';
    public bool $loading = false;

    protected $rules = [
        'telepon' => 'required|string|min:10',
    ];

    public function sendResetLink()
    {
        $this->validate();
        $this->loading = true;
        $this->error = '';
        $this->success = false;

        $normalizedPhone = $this->normalizePhone($this->telepon);

        $user = User::where('telepon', $normalizedPhone)->first();

        if (!$user) {
            $this->error = 'Maaf, nomor WhatsApp ini belum terdaftar di sistem kami.';
            $this->loading = false;
            return;
        }

        try {
            // Generate a secure token
            $token = Password::getRepository()->create($user);
            
            // Create a signed URL for the reset page
            $url = URL::temporarySignedRoute(
                'password.reset',
                now()->addMinutes(60),
                ['token' => $token, 'telepon' => $normalizedPhone]
            );

            $user->notify(new WaResetPassword($url, $user->name));
            $this->success = true;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send reset link: ' . $e->getMessage());
            $this->error = 'Gagal mengirim pesan WhatsApp. Silakan coba lagi nanti.';
        }

        $this->loading = false;
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    public function render()
    {
        return view('livewire.forgot-password')
            ->layout('layouts.public');
    }
}
