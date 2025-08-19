<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'punch_time' => 'datetime', // or 'date'
    ];

    public function employee() {
        return $this->belongsTo(User::class, 'user_id','id');
    }
}
