<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Role;

class Instruction extends Model
{
    protected $fillable = [
        'title',
        'content',
        'attachment_path',
        'recipient_type',
        'recipient_ids',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'recipient_ids' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created this instruction
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all acknowledgments for this instruction
     */
    public function acknowledgments(): HasMany
    {
        return $this->hasMany(InstructionAcknowledgment::class);
    }

    /**
     * Get related WhatsApp messages.
     */
    public function whatsappMessages()
    {
        return $this->morphMany(WhatsAppMessage::class, 'related');
    }

    /**
     * Scope to filter only active instructions
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get instructions relevant to a specific user
     */
    public function scopeForUser(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            // 1. Instructions for all
            $q->where('recipient_type', 'all')
              // 2. Instructions for specific roles
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('recipient_type', 'role')
                     ->whereJsonContains('recipient_ids', $user->roles->pluck('id')->toArray());
              })
              // 3. Instructions for specific SPPG
              ->orWhere(function ($sq) use ($user) {
                  if ($user->sppg_id) {
                      $sq->where('recipient_type', 'sppg')
                         ->whereJsonContains('recipient_ids', [$user->sppg_id]);
                  }
              })
              // 4. Instructions for specific Lembaga Pengusul
              ->orWhere(function ($sq) use ($user) {
                  // Jika user adalah Pimpinan Lembaga
                  if ($user->lembagaDipimpin()->exists()) {
                      $lembagaId = $user->lembagaDipimpin->id;
                       $sq->where('recipient_type', 'lembaga_pengusul')
                          ->whereJsonContains('recipient_ids', [$lembagaId]);
                  }
                  
                  // ATAU Jika user tergabung dalam SPPG di bawah naungan Lembaga tsb
                  if ($user->sppg_id && $user->sppg && $user->sppg->lembaga_pengusul_id) {
                       $sq->where('recipient_type', 'lembaga_pengusul')
                          ->whereJsonContains('recipient_ids', [$user->sppg->lembaga_pengusul_id]);
                  }
              })
              // 5. Instructions for specific user
              ->orWhere(function ($sq) use ($user) {
                  $sq->where('recipient_type', 'user')
                     ->whereJsonContains('recipient_ids', [$user->id]);
              });
        });
    }

    /**
     * Check if a user has acknowledged this instruction
     */
    public function isAcknowledgedBy(int $userId): bool
    {
        return $this->acknowledgments()->where('user_id', $userId)->exists();
    }

    /**
     * Get the acknowledgment record for a specific user
     */
    public function getAcknowledgmentFor(int $userId): ?InstructionAcknowledgment
    {
        return $this->acknowledgments()->where('user_id', $userId)->first();
    }

    /**
     * Calculate acknowledgment rate as percentage
     */
    public function getAcknowledgmentRate(): float
    {
        $targetedUsers = $this->getTargetedUsers();
        $targetCount = $targetedUsers->count();
        
        if ($targetCount === 0) {
            return 0;
        }

        $acknowledgmentCount = $this->acknowledgments()->count();
        
        return round(($acknowledgmentCount / $targetCount) * 100, 2);
    }

    /**
     * Get collection of users who should see this instruction
     */
    public function getTargetedUsers()
    {
        return match($this->recipient_type) {
            'all' => User::all(),
            'role' => User::whereHas('roles', function ($query) {
                $query->whereIn('id', $this->recipient_ids ?? []);
            })->get(),
            'sppg' => User::whereIn('sppg_id', $this->recipient_ids ?? [])->get(),
            'lembaga_pengusul' => User::where(function($query) {
                $query->whereHas('lembagaDipimpin', function ($q) {
                    $q->whereIn('id', $this->recipient_ids ?? []);
                })
                ->orWhereHas('sppg', function ($q) {
                    $q->whereIn('lembaga_pengusul_id', $this->recipient_ids ?? []);
                });
            })->get(),
            'user' => User::whereIn('id', $this->recipient_ids ?? [])->get(),
            default => collect([]),
        };
    }
}

