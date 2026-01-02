<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            // Only Admin/Management roles access Admin Panel
            return $this->hasRole([
                'Superadmin', 
                'Direktur Kornas', 
                'Staf Kornas', 
                'Staf Akuntan Kornas', 
                'Pimpinan Lembaga Pengusul'
            ]);
        }

        if ($panel->getId() === 'sppg') {
            // SPPG Panel for Operational roles
            // Super Admin also allowed for verification/debugging
            return $this->hasRole(['Super Admin', 'Kepala SPPG', 'PJ Pelaksana', 'Staf Akuntan', 'Staf Administrator SPPG']) 
                   || $this->sppgDiKepalai()->exists() 
                   || $this->unitTugas()->exists();
        }

        return true;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telepon',
        'alamat',
        'nik',
        'gender',
        'birth_date',
        'photo_path',
        // Tambahkan 'sppg_id' jika Anda menggunakannya sebagai SPPG "utama" atau "asal" user.
        // Ini opsional, karena peran utama ditentukan oleh relasi.
        // 'sppg_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Mendefinisikan relasi bahwa seorang User bisa menjadi KEPALA dari SATU SPPG.
     * Ini terhubung dengan kolom `kepala_sppg_id` di tabel `sppg`.
     */
    public function sppgDiKepalai(): HasOne
    {
        return $this->hasOne(Sppg::class, 'kepala_sppg_id');
    }

    /**
     * Mendefinisikan relasi BANYAK-KE-BANYAK antara User dan SPPG melalui pivot table.
     * Ini untuk mengetahui di SPPG mana saja seorang user bertugas (sebagai PJ, Akuntan, dll).
     */
    public function unitTugas(): BelongsToMany
    {
        return $this->belongsToMany(Sppg::class, 'sppg_user_roles')
            ->withPivot('role_id') // Ambil juga role_id dari pivot table
            ->limit(1);
    }

    public function lembagaDipimpin(): HasOne
    {
        return $this->hasOne(LembagaPengusul::class, 'pimpinan_id');
    }
}
