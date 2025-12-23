<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $table = 'distribusi';

    protected $fillable = [
        'user_id',
        'jadwal_produksi_id',
        'sekolah_id',
        'jumlah_porsi_besar',
        'jumlah_porsi_kecil',
        'status_pengantaran',
        'delivered_at',
        'photo_of_proof',
        'notes',
        // Pickup columns
        'pickup_status',
        'pickup_at',
        'pickup_photo_proof',
        'pickup_notes',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
        'pickup_at' => 'datetime',
    ];

    public function productionSchedule()
    {
        return $this->belongsTo(ProductionSchedule::class, 'jadwal_produksi_id', 'id');
    }

    public function school()
    {
        return $this->belongsTo(School::class, 'sekolah_id', 'id');
    }

    public function courier()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
