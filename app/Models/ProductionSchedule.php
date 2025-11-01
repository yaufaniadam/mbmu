<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionSchedule extends Model
{
    protected $table = 'jadwal_produksi';

    protected $fillable = [
        'sppg_id',
        'tanggal',
        'menu_hari_ini',
        'jumlah',
        'status'
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function verification()
    {
        return $this->hasOne(FoodVerification::class, 'jadwal_produksi_id', 'id');
    }
}
