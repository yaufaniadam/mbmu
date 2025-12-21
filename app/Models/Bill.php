<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'sppg_id',
        'type',
        'billed_to',
        'invoice_number',
        'period_start',
        'period_end',
        'amount',
        'status',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function remittances()
    {
        return $this->hasMany(Remittance::class);
    }
}
