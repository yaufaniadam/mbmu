<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SppgUserRole extends Model
{
    protected $fillable = [
        'sppg_id',
        'user_id',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
