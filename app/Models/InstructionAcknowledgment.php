<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructionAcknowledgment extends Model
{
    protected $fillable = [
        'instruction_id',
        'user_id',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Get the instruction that was acknowledged
     */
    public function instruction(): BelongsTo
    {
        return $this->belongsTo(Instruction::class);
    }

    /**
     * Get the user who acknowledged
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

