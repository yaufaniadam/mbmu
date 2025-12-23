<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerDailyAttendance extends Model
{
    protected $table = 'volunteer_daily_attendance';
    
    protected $fillable = [
        'volunteer_id',
        'sppg_id',
        'attendance_date',
        'status',
        'notes',
        'recorded_by',
    ];
    
    protected $casts = [
        'attendance_date' => 'date',
    ];
    
    /**
     * Get the volunteer
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
    
    /**
     * Get the SPPG
     */
    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
    
    /**
     * Get the user who recorded this attendance
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
