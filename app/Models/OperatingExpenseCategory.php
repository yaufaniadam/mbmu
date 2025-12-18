<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperatingExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'sppg_id',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
}
