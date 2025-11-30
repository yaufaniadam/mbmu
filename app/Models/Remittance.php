<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Remittance extends Model
{
    protected $fillable = [
        'bill_id',
        'user_id',
        'amount_sent',
        'source_bank_name',
        'destination_bank_name',
        'proof_file_path',
        'transfer_date',
        'status',
        'rejection_reason',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
