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

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function verification()
    {
        return $this->hasOne(ProductionVerification::class, 'production_schedule_id');
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'jadwal_produksi_id', 'id');
    }

    /**
     * Get the total 'jumlah_porsi_besar' from all related distributions.
     */
    public function totalPorsiBesar(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->distributions->sum('jumlah_porsi_besar'),
        );
    }

    /**
     * Get the total 'jumlah_porsi_kecil' from all related distributions.
     */
    public function totalPorsiKecil(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn() => $this->distributions->sum('jumlah_porsi_kecil'),
        );
    }

    /**
     * Check if all related distributions have the status 'Terkirim'.
     */
    public function isFullyDelivered(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: function () {
                $distributions = $this->distributions;

                if ($distributions->isEmpty()) {
                    return false;
                }

                return $distributions->every(fn($distribution) => $distribution->status_pengantaran === 'Terkirim');
            },
        );
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
