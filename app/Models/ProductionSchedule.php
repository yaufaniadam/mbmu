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
        'status',
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

    /**
     * NEW ACCESSOR
     * Check if all related distributions have the status 'Terkirim'.
     *
     * @return bool
     */
    public function getIsFullyDeliveredAttribute()
    {
        // Get the collection of distributions.
        // This follows the same lazy-loading pattern as your other accessors.
        $distributions = $this->distributions;

        // 1. If there are no distributions at all, we can't consider it "fully delivered".
        if ($distributions->isEmpty()) {
            return false;
        }

        // 2. Use the 'every' collection method.
        // This will return 'true' ONLY if every single item in the collection
        // passes the truth test. It stops and returns 'false' on the first failure.
        return $distributions->every(function ($distribution, $key) {
            return $distribution->status_pengantaran === 'Terkirim';
        });
    }

    public function schools()
    {
        return $this->hasManyThrough(
            School::class,
            Distribution::class,
            'jadwal_produksi_id', // Foreign key di tabel Distribution
            'id',                 // Foreign key di tabel School
            'id',                 // Local key di tabel ProductionSchedule
            'sekolah_id'          // Local key di tabel Distribution
        )->distinct(); // Gunakan distinct untuk menghindari duplikasi sekolah
    }
}
