<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'sppg_id',
        'type',
        'amount',
        'status',
        'proof_of_payment',
        'start_date',
        'end_date',
        'due_date',
        'verified_at',
        'rejection_reason',
        'source_bank',
        'destination_bank',
        'transfer_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'due_date' => 'date',
        'verified_at' => 'datetime',
        'transfer_date' => 'date',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
}
