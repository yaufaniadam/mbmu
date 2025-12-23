<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        'tanggal',
        'nama',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Check if a given date is a holiday
     */
    public static function isHoliday($date): bool
    {
        return static::whereDate('tanggal', $date)->exists();
    }
}
