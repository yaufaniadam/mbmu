<?php

namespace App\Livewire;

use App\Models\RegistrationToken;
use App\Models\SppgUserRole;
use App\Models\User;
use App\Services\WablasService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;

class SelfRegistration extends Component
{
    // Route parameters
    public string $role = '';
    public string $tokenCode = '';

    // Token validation
    public ?RegistrationToken $registrationToken = null;
    public bool $tokenValidated = false;
    public string $tokenError = '';

    // Form fields
    public string $name = '';
    public string $telepon = '';
    public string $email = '';
    public string $password = '';

    // State
    public int $step = 1; // 1: Validasi Token, 2: Isi Data, 3: Sukses
    public bool $registrationComplete = false;
    public string $generatedPassword = '';
    public bool $hasTokenData = false;

    // Role labels for display
    public array $roleLabels = [
        'kepala_lembaga' => 'Kepala Lembaga Pengusul',
        'kepala_sppg' => 'Kepala SPPG',
        'ahli_gizi' => 'Ahli Gizi',
        'akuntan' => 'Staf Akuntan',
        'administrator' => 'Staf Administrator',
    ];


    public function mount(string $role = '', string $token = '')
    {
        $this->role = $role;
        $this->tokenCode = $token;

        // If token is provided in URL, validate it immediately
        if ($this->tokenCode) {
            $this->validateToken();
        }
    }

    public function validateToken()
    {
        $this->tokenError = '';

        $token = RegistrationToken::where('token', strtoupper($this->tokenCode))
            ->with(['sppg.kepalaSppg', 'sppg.kepalaPengusul'])
            ->first();

        if (!$token) {
            $this->tokenError = 'Kode registrasi tidak ditemukan.';
            return;
        }

        if (!$token->isValid()) {
            if (!$token->is_active) {
                $this->tokenError = 'Kode registrasi sudah tidak aktif.';
            } elseif ($token->expires_at && $token->expires_at->isPast()) {
                $this->tokenError = 'Kode registrasi sudah kadaluarsa.';
            } elseif ($token->used_count >= $token->max_uses) {
                $this->tokenError = 'Kode registrasi sudah mencapai batas penggunaan.';
            }
            return;
        }

        // Check if role matches (if role is specified in URL)
        if ($this->role && $token->role !== $this->role) {
            $this->tokenError = 'Kode registrasi tidak sesuai dengan jenis pendaftaran.';
            return;
        }

        $this->registrationToken = $token;
        $this->role = $token->role;
        $this->tokenValidated = true;
        $this->step = 2;

        // Pre-fill data from token or existing user record
        $name = $token->recipient_name;
        $phone = $token->recipient_phone;

        // Fallback to existing user records linked to SPPG
        if ((!$name || !$phone) && $token->sppg) {
            $existingUser = null;
            if ($token->role === 'kepala_sppg') {
                $existingUser = $token->sppg->kepalaSppg;
            } elseif ($token->role === 'kepala_lembaga') {
                $existingUser = $token->sppg->kepalaPengusul;
            }

            if ($existingUser) {
                $name = $name ?: $existingUser->name;
                $phone = $phone ?: $existingUser->telepon;
            }
        }

        // Apply pre-filled data
        if ($name) {
            $this->name = $name;
            $this->hasTokenData = true;
        }

        if ($phone) {
            $this->hasTokenData = true;
            $phone = (string) $phone;
            // Strip leading 62 or 0 if present to match the input format (which adds +62)
            if (str_starts_with($phone, '62')) {
                $this->telepon = substr($phone, 2);
            } elseif (str_starts_with($phone, '0')) {
                $this->telepon = substr($phone, 1);
            } else {
                $this->telepon = $phone;
            }
        }
    }

    public function register()
    {
        // Normalize phone number before validation
        $this->telepon = $this->normalizePhone($this->telepon);

        // Check if user already exists
        $user = User::where('telepon', $this->telepon)->first();

        $this->validate([
            'name' => 'required|string|max:255',
            'telepon' => 'required|string|max:20|unique:users,telepon,' . ($user?->id ?? 'NULL'),
            'email' => 'nullable|email|unique:users,email,' . ($user?->id ?? 'NULL'),
            'password' => 'required|string|min:8',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'telepon.required' => 'Nomor HP wajib diisi.',
            'telepon.unique' => 'Nomor HP sudah terdaftar.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi untuk keperluan login.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Generate password if not provided
        if (empty($this->password)) {
            $this->generatedPassword = 'mbm' . substr($this->telepon, -4);
            $this->password = $this->generatedPassword;
        } else {
            $this->generatedPassword = $this->password;
        }

        try {
            Cache::lock('registration_token_' . $this->registrationToken->token, 10)->block(5, function () {
                // Critical Section: Re-validate token state inside the lock
                $this->registrationToken->refresh();
                
                if ($this->registrationToken->used_count >= $this->registrationToken->max_uses) {
                    throw new \Exception('Maaf, kuota pendaftaran untuk token ini baru saja habis.');
                }

                DB::beginTransaction();

                try {
                    // Check if user already exists
                    $user = User::where('telepon', $this->telepon)->first();
                    
                    if ($user) {
                        // UPDATE LOGIC: If user exists, update their name, email and password
                        // This effectively "activates" the account with user's desired credentials
                        $user->update([
                            'name' => $this->name,
                            'email' => $this->email ?: $user->email,
                            'password' => Hash::make($this->password),
                        ]);
                    } else {
                        // Create user
                        $user = User::create([
                            'name' => $this->name,
                            'telepon' => $this->telepon,
                            'email' => $this->email ?: null,
                            'password' => Hash::make($this->password),
                        ]);
                    }


                    // Assign Spatie role
                    $spatieRoleName = $this->registrationToken->getSpatieRoleName();
                    $spatieRole = Role::where('name', $spatieRoleName)->first();
                    
                    if ($spatieRole) {
                        $user->assignRole($spatieRole);
                    }

                    // Create SPPG user role association
                    SppgUserRole::create([
                        'sppg_id' => $this->registrationToken->sppg_id,
                        'user_id' => $user->id,
                        'role_id' => $spatieRole?->id,
                    ]);

                    // If registering as Kepala SPPG, also update the sppg table
                    if ($this->role === 'kepala_sppg') {
                        $this->registrationToken->sppg->update([
                            'kepala_sppg_id' => $user->id,
                        ]);
                    }

                    // If registering as Kepala Lembaga Pengusul, update lembaga_pengusul table
                    if ($this->role === 'kepala_lembaga' && $this->registrationToken->sppg->lembagaPengusul) {
                        $this->registrationToken->sppg->lembagaPengusul->update([
                            'pimpinan_id' => $user->id,
                        ]);
                    }

                    // Mark token as used
                    $this->registrationToken->markAsUsed();


                    DB::commit();

                    // Send WhatsApp notification
                    try {
                        $wablas = new WablasService();
                        $wablas->sendRegistrationSuccess(
                            $this->telepon,
                            $this->name,
                            $this->generatedPassword,
                            $this->registrationToken->sppg->nama_sppg,
                            $this->roleLabels[$this->role] ?? $this->role
                        );
                    } catch (\Exception $e) {
                        // Log error but don't fail the registration
                        \Illuminate\Support\Facades\Log::error('Failed to send registration WA: ' . $e->getMessage());
                    }

                    $this->registrationComplete = true;
                    $this->step = 3;

                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e; // Re-throw to be caught by the outer catch
                }
            });

        } catch (\Illuminate\Contracts\Cache\LockTimeoutException $e) {
            session()->flash('error', 'Sistem sedang sibuk, silakan coba beberapa saat lagi.');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    protected function normalizePhone(string $phone): string
    {
        // Remove all non-numeric characters except leading +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Remove + if present
        $phone = ltrim($phone, '+');
        
        // If starts with 62, keep it
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
        return view('livewire.self-registration')
            ->layout('layouts.public');
    }
}
