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
        $alternativePhone = str_starts_with($normalizedPhone, '62') 
            ? '0' . substr($normalizedPhone, 2) 
            : '62' . substr($normalizedPhone, 1);

        // 1. Find a valid registration token for this phone number directly
        $token = RegistrationToken::whereIn('recipient_phone', [$normalizedPhone, $alternativePhone])
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->whereColumn('used_count', '<', 'max_uses')
            ->first();

        // 2. Fallback: Find user by phone, then find SPPG they are assigned to, then find token
        if (!$token) {
            $user = User::whereIn('telepon', [$normalizedPhone, $alternativePhone])->first();
            if ($user) {
                // Check if they are Kepala SPPG
                $sppg = \App\Models\Sppg::where('kepala_sppg_id', $user->id)->first();
                $role = 'kepala_sppg';
                
                // If not found, check if they are Pimpinan Lembaga
                if (!$sppg) {
                    $lembaga = \App\Models\LembagaPengusul::where('pimpinan_id', $user->id)->first();
                    if ($lembaga) {
                        $sppg = \App\Models\Sppg::where('lembaga_pengusul_id', $lembaga->id)->first();
                        $role = 'kepala_lembaga';
                    }
                }

                if ($sppg) {
                    $token = RegistrationToken::where('sppg_id', $sppg->id)
                        ->where('role', $role)
                        ->where('is_active', true)
                        ->where(function ($query) {
                            $query->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        })
                        ->whereColumn('used_count', '<', 'max_uses')
                        ->first();
                }
            }
        }

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
