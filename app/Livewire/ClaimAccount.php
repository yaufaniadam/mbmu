<?php

namespace App\Livewire;

use App\Models\RegistrationToken;
use App\Models\User;
use App\Notifications\KirimToken;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class ClaimAccount extends Component
{
    public string $telepon = '';
    public bool $success = false;
    public string $error = '';
    public bool $loading = false;

    protected $rules = [
        'telepon' => 'required|string|min:10',
    ];

    protected $messages = [
        'telepon.required' => 'Nomor WhatsApp wajib diisi.',
        'telepon.min' => 'Nomor WhatsApp minimal 10 karakter.',
    ];

    public function claim()
    {
        $this->validate();
        $this->loading = true;
        $this->error = '';
        $this->success = false;

        $normalizedPhone = $this->normalizePhone($this->telepon);

        // 1. Find a valid registration token for this phone number

        $token = RegistrationToken::where('recipient_phone', $normalizedPhone)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereColumn('used_count', '<', 'max_uses')
            ->first();

        if (!$token) {
            $this->error = 'Maaf, nomor WhatsApp ini belum terdaftar di sistem kami untuk aktivasi. Silakan hubungi admin kornas jika ada kesalahan.';
            $this->loading = false;
            return;
        }

        // 3. Send the token via WhatsApp
        try {
            $token->notify(new KirimToken($token));
            $this->success = true;
        } catch (\Exception $e) {
            Log::error('Failed to send claim token: ' . $e->getMessage());
            $this->error = 'Gagal mengirim pesan WhatsApp. Silakan coba lagi nanti atau hubungi bantuan.';
        }

        $this->loading = false;
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters except leading +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = ltrim($phone, '+');
        
        // If starts with 0, replace with 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    public function render()
    {
        return view('livewire.claim-account')
            ->layout('layouts.public');
    }
}
