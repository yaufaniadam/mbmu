<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CustomLogin extends BaseLogin
{
    public function getHeading(): string | Htmlable
    {
        $panelId = Filament::getCurrentPanel()->getId();
        
        // Custom check for query param 'role'
        if ($panelId === 'admin' && request()->query('role') === 'pengusul') {
            $roleName = 'Lembaga Pengusul';
        } else {
            $roleName = match ($panelId) {
                'admin' => 'Administrator',
                'sppg' => 'SPPG',
                'production' => 'Tim Produksi',
                default => 'User',
            };
        }

        return new HtmlString("
            <div class='mb-6 font-bold tracking-tight text-center text-lg'>
                Login <span style='color: #ff69b4'>{$roleName}</span>
            </div>
        ");
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('login')
                    ->label('No. HP atau Email')
                    ->placeholder('081234567890 atau email@contoh.com')
                    ->required()
                    ->autocomplete()
                    ->autofocus()
                    ->extraInputAttributes(['tabindex' => 1]),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        // Determine if login is email or phone
        $loginField = $this->getLoginField($data['login']);
        
        // Normalize phone if it's a phone number
        $loginValue = $data['login'];
        if ($loginField === 'telepon') {
            $loginValue = $this->normalizePhone($loginValue);
        }

        $credentials = [
            $loginField => $loginValue,
            'password' => $data['password'],
        ];

        if (!Auth::attempt($credentials, $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof \Filament\Models\Contracts\FilamentUser) &&
            (!$user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    protected function getLoginField(string $login): string
    {
        // Check if it looks like an email
        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // Otherwise treat as phone
        return 'telepon';
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

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
