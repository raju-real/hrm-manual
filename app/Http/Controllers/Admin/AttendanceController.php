<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function attendanceLogs(Request $request)
    {
        // 1. Define the date range
        // 1. Define the date range
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : $startDate->copy()->endOfDay();

        // 2. Build the base query with all joins and select statements
        $query = User::select([
            'users.id as user_id',
            'users.employee_id AS employee_id',
            'departments.name as department_name',
            'departments.slug as department_slug',
            'designations.name as designation_name',
            'designations.slug as designation_slug',
            'branches.name as branch_name',
            'branches.slug as branch_slug',
            'users.name AS employee_name',
            'attendance_summary.attendance_date',
            'attendance_summary.check_in',
            'check_in_log.latitude AS check_in_lat',
            'check_in_log.longitude AS check_in_lon',
            'attendance_summary.check_out',
            'check_out_log.latitude AS check_out_lat',
            'check_out_log.longitude AS check_out_lon',
            DB::raw('TIMEDIFF(attendance_summary.check_out, attendance_summary.check_in) AS working_hours'),
            DB::raw("CASE WHEN attendance_summary.check_in IS NOT NULL THEN 'present' ELSE 'absent' END AS status")
        ])
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoinSub(
                function ($subQuery) use ($startDate, $endDate) {
                    $subQuery->from('attendance_logs')
                        ->select(
                            'user_id',
                            DB::raw('DATE(punch_time) AS attendance_date'),
                            DB::raw('MIN(punch_time) AS check_in'),
                            DB::raw('MAX(punch_time) AS check_out')
                        )
                        ->whereBetween('punch_time', [$startDate, $endDate])
                        ->groupBy('user_id', 'attendance_date');
                },
                'attendance_summary',
                'attendance_summary.user_id',
                '=',
                'users.id'
            )
            ->leftJoin('attendance_logs as check_in_log', function ($join) {
                $join->on('check_in_log.user_id', '=', 'attendance_summary.user_id')
                    ->on('check_in_log.punch_time', '=', 'attendance_summary.check_in');
            })
            ->leftJoin('attendance_logs as check_out_log', function ($join) {
                $join->on('check_out_log.user_id', '=', 'attendance_summary.user_id')
                    ->on('check_out_log.punch_time', '=', 'attendance_summary.check_out');
            });

        // 3. Apply dynamic filters based on request input
        if ($request->filled('user_id')) {
            $query->where('users.id', $request->input('user_id'));
        }

        if ($request->filled('branch_slug')) {
            $query->where('branches.slug', $request->input('branch_slug'));
        }

        if ($request->filled('department_slug')) {
            $query->where('departments.slug', $request->input('department_slug'));
        }

        if ($request->filled('designation_slug')) {
            $query->where('designations.slug', $request->input('designation_slug'));
        }

        if ($request->filled('status')) {
            $status = strtolower($request->input('status')); // Normalize status input
            if ($status === 'present') {
                $query->whereNotNull('attendance_summary.check_in');
            } elseif ($status === 'absent') {
                $query->whereNull('attendance_summary.check_in');
            }
        }

        // 4. Order and get results
        $attendance_summery = $query->orderBy('attendance_summary.check_in', 'desc')
            ->paginate(100);

        //return response()->json($report);
        return view('admin.attendance.attendance_logs', compact('attendance_summery'));
    }

    public function attendanceDetails(Request $request)
    {
        $employeeId = $request->get('employee_id');
        $date = $request->get('date');

        if (!$employeeId || !$date) {
            return response()->json(['error' => 'Employee ID and date are required.'], 400);
        }

        $attendanceSummary = AttendanceLog::select(
            DB::raw('MIN(punch_time) AS check_in'),
            DB::raw('MAX(punch_time) AS check_out')
        )
            ->where('user_id', $employeeId)
            ->whereDate('punch_time', $date)
            ->groupBy('user_id')
            ->first();

        if (!$attendanceSummary) {
            return response()->json([
                'check_in' => null,
                'check_out' => null,
                'check_in_lat' => null,
                'check_in_lon' => null,
                'check_out_lat' => null,
                'check_out_lon' => null,
                'working_hours' => '00:00:00',
            ]);
        }

        $checkInLog = AttendanceLog::where('user_id', $employeeId)
            ->where('punch_time', $attendanceSummary->check_in)
            ->first(['latitude', 'longitude']);

        $checkOutLog = AttendanceLog::where('user_id', $employeeId)
            ->where('punch_time', $attendanceSummary->check_out)
            ->first(['latitude', 'longitude']);

        $workingHours = '00:00:00';
        if ($attendanceSummary->check_in && $attendanceSummary->check_out) {
            $checkInTime = new \DateTime($attendanceSummary->check_in);
            $checkOutTime = new \DateTime($attendanceSummary->check_out);
            $interval = $checkOutTime->diff($checkInTime);
            $workingHours = $interval->format('%H:%I:%S');
        }

        $attendanceDetails = (object) [
            'check_in' => timeFormat($attendanceSummary->check_in),
            'check_out' => timeFormat($attendanceSummary->check_out),
            'check_in_lat' => $checkInLog ? $checkInLog->latitude : null,
            'check_in_lon' => $checkInLog ? $checkInLog->longitude : null,
            'check_out_lat' => $checkOutLog ? $checkOutLog->latitude : null,
            'check_out_lon' => $checkOutLog ? $checkOutLog->longitude : null,
            'working_hour' => workingHours($workingHours)->working_hour ?? 'N/A',
            'has_check_in' => (bool) $checkInLog, // true if $checkInLog is not null
            'has_check_out' => (bool) $checkOutLog, // true if $checkOutLog is not null
        ];

        //return response()->json($attendanceDetails);
        return view('admin.attendance.attendance_details_leaflet', compact('attendanceDetails'));
        //return view('admin.attendance.attendance_details_google_map', compact('attendanceDetails'));
    }
}
