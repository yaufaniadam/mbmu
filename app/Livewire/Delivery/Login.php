<?php

namespace App\Livewire\Delivery;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.delivery')]
#[Title('Login Pengantaran')]
class Login extends Component
{
    public $login = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'login' => 'required',
        'password' => 'required',
    ];

    public function mount()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole('Staf Pengantaran')) {
                return redirect()->route('delivery.dashboard');
            }
        }
    }

    public function login()
    {
        $this->validate();

        $loginField = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telepon';
        $loginValue = $this->login;

        if ($loginField === 'telepon') {
            $normalizedPhone = $this->normalizePhone($this->login);
            $alternativePhone = str_starts_with($normalizedPhone, '62') 
                ? '0' . substr($normalizedPhone, 2) 
                : '62' . substr($normalizedPhone, 1);
            
            $user = \App\Models\User::whereIn('telepon', [$normalizedPhone, $alternativePhone])->first();
            $loginValue = $user ? $user->telepon : $normalizedPhone;
        }

        if (Auth::attempt([$loginField => $loginValue, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();
            
            if ($user->hasRole('Staf Pengantaran')) {
                session()->regenerate();
                return redirect()->route('delivery.dashboard');
            }

            Auth::logout();
            $this->addError('login', 'Akun Anda tidak memiliki akses pengantaran.');
            return;
        }

        $this->addError('login', 'Login atau password salah.');
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        $phone = ltrim($phone, '+');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    public function render()
    {
        return view('livewire.delivery.login');
    }
}
