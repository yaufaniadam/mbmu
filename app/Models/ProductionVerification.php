<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductionVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppg_id',
        'production_schedule_id',
        'user_id',
        'date',
        'checklist_results',
        'notes',
    ];

    /**
     * Cast attributes to native types.
     * checklist_results is stored as JSON.
     */
    protected $casts = [
        'date' => 'date',
        'checklist_results' => 'array',
    ];

    /**
     * Relationship: Linked to SPPG.
     */
    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    /**
     * Relationship: User who performed the verification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function productionSchedule(): BelongsTo
    {
        return $this->belongsTo(ProductionSchedule::class);
    }
}
