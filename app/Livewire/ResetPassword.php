<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token;
    public string $telepon;
    public string $password = '';
    public string $password_confirmation = '';
    public bool $success = false;
    public string $error = '';
    public bool $loading = false;

    protected $rules = [
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'password.required' => 'Password baru wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
    ];

    public function mount(string $token, string $telepon)
    {
        $this->token = $token;
        $this->telepon = $telepon;
        
        // Basic security check: verify if it's a valid signed URL or at least has the required params
        if (empty($this->token) || empty($this->telepon)) {
            abort(403, 'Link reset password tidak valid atau sudah kedaluwarsa.');
        }
    }

    public function resetPassword()
    {
        $this->validate();
        $this->loading = true;
        $this->error = '';

        $alternativePhone = str_starts_with($this->telepon, '62') 
            ? '0' . substr($this->telepon, 2) 
            : '62' . substr($this->telepon, 1);

        $user = User::whereIn('telepon', [$this->telepon, $alternativePhone])->first();

        if (!$user) {
            $this->error = 'Pengguna tidak ditemukan.';
            $this->loading = false;
            return;
        }

        // Ensure we use the same reset email logic as ForgotPassword
        $resetEmail = $user->email ?? $this->telepon . '@mbm1912.id';
        $user->email = $resetEmail;

        // Verify token again via Laravel's Password broker logic
        if (!Password::getRepository()->exists($user, $this->token)) {
            $this->error = 'Token reset password tidak valid atau sudah kedaluwarsa. Silakan minta link baru.';
            $this->loading = false;
            return;
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($this->password),
            ]);

            // Delete used token
            Password::getRepository()->delete($user);

            $this->success = true;
            
            // Auto login after reset
            Auth::login($user);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Password reset failed: ' . $e->getMessage());
            $this->error = 'Gagal memperbarui password. Silakan coba lagi nanti.';
        }

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.reset-password')
            ->layout('layouts.public');
    }
}
