<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $table = 'Distribusi';

    protected $fillable = [
        'jadwal_produksi_id',
        'sekolah_id',
        'jumlah_porsi_besar',
        'jumlah_porsi_kecil',
        'status_pengantaran',
        'delivered_at',
    ];

    public function productionSchedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'jadwal_produksi_id', 'id');
    }
}
