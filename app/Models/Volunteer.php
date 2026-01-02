<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $table = 'relawan';


    protected $fillable = [
        'sppg_id',
        'user_id',
        'nama_relawan',
        'nik',
        'gender',
        'category',
        'posisi',
        'kontak',
        'address',
        'daily_rate',
        'birth_date',
        'photo_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }

    public function attendances()
    {
        return $this->hasMany(VolunteerAttendance::class);
    }

    public function dailyAttendances()
    {
        return $this->hasMany(VolunteerDailyAttendance::class);
    }
}
