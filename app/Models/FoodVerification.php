<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodVerification extends Model
{
    protected $table = 'verifikasi_pangan';

    protected $fillable = [
        'jadwal_produksi_id',
        'user_id',
        'checklist_data',
        'catatan',
    ];

    public function productionSchedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'jadwal_produksi_id', 'id');
    }
}
