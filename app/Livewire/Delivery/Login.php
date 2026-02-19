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
    public $email = '';
    public $password = '';
    public $remember = false;

    protected $rules = [
        'email' => 'required|email',
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

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            $user = Auth::user();
            
            if ($user->hasRole('Staf Pengantaran')) {
                session()->regenerate();
                return redirect()->route('delivery.dashboard');
            }

            Auth::logout();
            $this->addError('email', 'Akun Anda tidak memiliki akses pengantaran.');
            return;
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        return view('livewire.delivery.login');
    }
}
