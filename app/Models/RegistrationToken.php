<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class RegistrationToken extends Model
{
    use Notifiable;

    protected $fillable = [
        'token',
        'sppg_id',
        'role',
        'max_uses',
        'used_count',
        'expires_at',
        'is_active',
        'created_by',
        'recipient_name',
        'recipient_phone',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
        'max_uses' => 'integer',
        'used_count' => 'integer',
    ];

    /**
     * Role labels for display
     */
    public const ROLE_LABELS = [
        'kepala_lembaga' => 'Kepala Lembaga Pengusul',
        'kepala_sppg' => 'Kepala SPPG',
        'ahli_gizi' => 'Ahli Gizi',
        'akuntan' => 'Staf Akuntan',
        'administrator' => 'Staf Administrator SPPG',
    ];

    /**
     * Map registration roles to Spatie permission roles
     */
    public const ROLE_MAPPING = [
        'kepala_sppg' => 'Kepala SPPG',
        'ahli_gizi' => 'Ahli Gizi',
        'akuntan' => 'Staf Akuntan',
        'administrator' => 'Staf Administrator SPPG',
    ];

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Route notifications for the WhatsApp channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForWhatsApp($notification)
    {
        return $this->recipient_phone;
    }

    /**
     * Generate a unique token
     */
    public static function generateToken(): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (static::where('token', $token)->exists());

        return $token;
    }

    /**
     * Check if token is valid for use
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    /**
     * Mark token as used (increment counter)
     */
    public function markAsUsed(): void
    {
        $this->increment('used_count');
    }

    /**
     * Get the registration URL for this token
     */
    public function getRegistrationUrl(): string
    {
        return url("/daftar/{$this->role}/{$this->token}");
    }

    /**
     * Get human-readable role label
     */
    public function getRoleLabelAttribute(): string
    {
        return self::ROLE_LABELS[$this->role] ?? $this->role;
    }

    /**
     * Get the Spatie role name for this token
     */
    public function getSpatieRoleName(): string
    {
        return self::ROLE_MAPPING[$this->role] ?? $this->role;
    }
}
