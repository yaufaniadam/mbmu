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

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'jadwal_produksi_id', 'id');
    }

    public function getTotalPorsiBesarAttribute()
    {
        // This will lazy-load the 'distributions' relationship if it's not already loaded
        // and then sum the 'jumlah_porsi_besar' values from the collection.
        return $this->distributions->sum('jumlah_porsi_besar');
    }

    /**
     * Get the total 'jumlah_porsi_kecil' from all related distributions.
     *
     * This accessor sums up the 'jumlah_porsi_kecil' field from all
     * related distribution records.
     *
     * @return float|int
     */
    public function getTotalPorsiKecilAttribute()
    {
        // This will lazy-load the 'distributions' relationship if it's not already loaded
        // and then sum the 'jumlah_porsi_kecil' values from the collection.
        return $this->distributions->sum('jumlah_porsi_kecil');
    }
}
