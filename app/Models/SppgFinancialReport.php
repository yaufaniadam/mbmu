<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SppgFinancialReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppg_id',
        'user_id',
        'start_date',
        'end_date',
        'file_path',
        'notes',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function sppg(): BelongsTo
    {
        return $this->belongsTo(Sppg::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
