<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VolunteerAttendance extends Model
{
    protected $table = 'volunteer_attendance';
    
    protected $fillable = [
        'volunteer_id',
        'sppg_id',
        'period_start',
        'period_end',
        'days_present',
        'late_minutes',
        'daily_rate',
        'late_deduction_per_hour',
        'gross_salary',
        'late_deduction',
        'net_salary',
        'notes',
    ];
    
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
    ];
    
    /**
     * Get the volunteer that this attendance record belongs to
     */
    public function volunteer()
    {
        return $this->belongsTo(Volunteer::class);
    }
    
    /**
     * Get the SPPG that this attendance record belongs to
     */
    public function sppg()
    {
        return $this->belongsTo(Sppg::class);
    }
    
    /**
     * Auto-calculate salaries when saving
     */
    protected static function booted()
    {
        static::saving(function ($attendance) {
            // Calculate gross salary: days present * daily rate
            $attendance->gross_salary = $attendance->days_present * $attendance->daily_rate;
            
            // Calculate late deduction: (late minutes / 60) * deduction rate per hour
            $lateHours = $attendance->late_minutes / 60;
            $attendance->late_deduction = $lateHours * $attendance->late_deduction_per_hour;
            
            // Calculate net salary: gross - deduction
            $attendance->net_salary = $attendance->gross_salary - $attendance->late_deduction;
        });
    }
}
