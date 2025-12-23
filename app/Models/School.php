<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $table = 'sekolah';

    protected $fillable = [
        'nama_sekolah',
        'kategori',
        'alamat',
        'province_code',
        'city_code',
        'district_code',
        'village_code',
        'sppg_id',
        'latitude',
        'longitude',
        'default_porsi_besar',
        'default_porsi_kecil',
    ];

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'sekolah_id', 'id');
    }
}
