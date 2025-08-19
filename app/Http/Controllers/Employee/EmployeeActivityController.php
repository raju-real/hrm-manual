<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeActivityController extends Controller
{
    public function attendanceSummery()
    {
        $attendance_summery = AttendanceLog::select(
            DB::raw('MIN(id) as id'), // id of the earliest punch of the day
            DB::raw('DATE(punch_time) as attendance_date'),
            DB::raw('MIN(punch_time) as check_in'),
            DB::raw('MAX(punch_time) as check_out'),
            DB::raw('SUBSTRING_INDEX(GROUP_CONCAT(type ORDER BY punch_time ASC), ",", 1) as type')
        )
            ->where('user_id', Auth::id())
            ->groupBy(DB::raw('DATE(punch_time)'))
            ->orderBy('attendance_date', 'desc')
            ->paginate(50);
        return view('employee.attendance_summery', compact('attendance_summery'));
    }

    public function punchManual(Request $request)
    {
        $this->validate($request, [
            'direction' => 'required|in:in,out',
            'latitude' => 'required',
            'longitude' => 'required'
        ]);

        $direction = $request->direction;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        
        // Business rule: check previous manual records
        // $lastManual = AttendanceLog::where('employee_id', $user->id)->where('type', 'manual')->latest('punch_time')->first();
        // if ($direction === 'in' && $lastManual && $lastManual->direction === 'in' && !$lastManual->checkout_recorded_same_day) {
        //     $lastDate = $lastManual->punch_time->toDateString();
        //     if ($lastDate < now()->toDateString()) {
        //         return response()->json(['error' => 'You must checkout the previous manual attendance before next day check-in.'], 422);
        //     }
        // }

        $attendance = new AttendanceLog();
        $attendance->user_id = Auth::id();
        $attendance->punch_time = now();
        $attendance->direction = $direction;
        $attendance->latitude = $latitude;
        $attendance->longitude = $longitude;
        $attendance->created_by = Auth::id();
        $attendance->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Attendance recorded successfully!'
        ]);
    }

    public function attendanceLocation($attendance_id)
    {
        $attendance = AttendanceLog::where('id', encrypt_decrypt($attendance_id, 'decrypt'))->first();
        return view('employee.attendance_location', compact('attendance'));
    }
}
