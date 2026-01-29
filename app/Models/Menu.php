<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sppg;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'sppg_id',
        'image',
        'name',
        'description',
        'date',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
}
