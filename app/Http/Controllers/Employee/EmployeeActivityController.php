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
        $direction = $request->direction;
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $ipv4 = gethostbyname(gethostname());
        $userId = Auth::id();

        if ($direction === 'in') {
            // Check if already checked in without checking out
            $openAttendance = AttendanceLog::where('user_id', $userId)
                ->whereNull('check_out')
                ->latest()
                ->first();

            if ($openAttendance) {
                return response()->json(['status' => 'error', 'message' => 'You are already checked in. Please check out first.']);
            }

            // Create a new attendance record with check-in time
            $attendance = new AttendanceLog();
            $attendance->user_id = $userId;
            $attendance->check_in = now();
            $attendance->latitude = $latitude;
            $attendance->longitude = $longitude;
            $attendance->client_ip = $ipv4 ?? null;
            $attendance->created_by = $userId;
            $attendance->save();

            return response()->json(['status' => 'success', 'message' => 'Checked in successfull.']);
        } elseif ($direction === 'out') {
            // Find the last open check-in
            $attendance = AttendanceLog::where('user_id', $userId)
                ->whereNull('check_out')
                ->latest()
                ->first();

            if (!$attendance) {
                return response()->json(['status' => 'error', 'message' => 'No check-in record found to check out.']);
            }

            $attendance->check_out = now();
            $attendance->latitude = $latitude;
            $attendance->longitude = $longitude;
            $attendance->client_ip = $ipv4 ?? null;
            // Calculate total minutes
            //$attendance->total_minutes = $attendance->check_in->diffInMinutes($attendance->check_out);
            $attendance->total_minutes =ceil((strtotime($attendance->check_out) - strtotime($attendance->check_in)) / 60);
            $attendance->save();
            return response()->json(['status' => 'success', 'message' => 'Checked out successfull.', 'total_minutes' => $attendance->total_minute]);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid direction.']);
    }

    public function attendanceLocation($attendance_id)
    {
        $attendance = AttendanceLog::where('id', encrypt_decrypt($attendance_id, 'decrypt'))->first();
        return view('employee.attendance_location', compact('attendance'));
    }
}
