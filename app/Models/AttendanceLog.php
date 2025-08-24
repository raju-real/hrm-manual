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

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getMinuteInHourAttribute()
    {
        $total_minutes = $this->total_minutes;
        if ($total_minutes) {
            return round($total_minutes / 100, 2); // convert minutes like 24 → 0.24
        }
        return 'N/A';
    }
}
