<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes;
    protected $appends = ['minute_in_hour'];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getMinuteInHourAttribute()
    {
        $total_minutes = $this->total_minutes;
        if ($total_minutes) {
            //return round($total_minutes / 100, 2); // convert minutes like 24 → 0.24
            // Convert minutes → hours (decimal)
            $totalHours = round($total_minutes / 60, 2); // 1.17

            // OR format as H:i (1:10)
           return $hoursFormatted = sprintf('%02d:%02d', floor($total_minutes / 60), $total_minutes % 60);
        }
        return 'N/A';
    }
}
