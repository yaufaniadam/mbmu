<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionVerificationSetting extends Model
{
    protected $fillable = [
        'sppg_id',
        'checklist_data',
    ];

    protected $casts = [
        'checklist_data' => 'array', // Casts the JSON to and from an array
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
}
