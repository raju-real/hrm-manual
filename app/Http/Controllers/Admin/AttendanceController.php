<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AttendanceController extends Controller
{
    public function attendanceLogs(Request $request)
    {
        // 1️⃣ Define the date range
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : $startDate->copy()->endOfDay();

        // 2️⃣ Subquery: summarize attendance per user per date
        $attendanceSub = DB::table('attendance_logs as al')
            ->select(
                'al.user_id',
                DB::raw('DATE(al.check_in) as attendance_date'),
                DB::raw('MIN(al.check_in) as check_in'),
                DB::raw('MAX(al.check_out) as check_out'),
                DB::raw('SUM(al.total_minutes) as total_minutes')
            )
            ->whereBetween('al.check_in', [$startDate, $endDate])
            ->groupBy('al.user_id', DB::raw('DATE(al.check_in)'));

        // 3️⃣ Main query: join summarized attendance with users and related tables
        $query = User::select([
            'users.id as user_id',
            'users.employee_id',
            'users.name as employee_name',
            'departments.name as department_name',
            'departments.slug as department_slug',
            'designations.name as designation_name',
            'designations.slug as designation_slug',
            'branches.name as branch_name',
            'branches.slug as branch_slug',
            'attendance_summary.attendance_date',
            'attendance_summary.check_in',
            'attendance_summary.check_out',
            'attendance_summary.total_minutes',
            DB::raw("ROUND(attendance_summary.total_minutes / 60, 2) as working_hours"),
            DB::raw("CASE WHEN attendance_summary.check_in IS NOT NULL THEN 'present' ELSE 'absent' END as status"),
            'first_log.id as first_checkin_id',
            'last_log.id as last_checkout_id',
            'first_log.latitude as check_in_lat',
            'first_log.longitude as check_in_lon',
            'last_log.latitude as check_out_lat',
            'last_log.longitude as check_out_lon'
        ])
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoinSub($attendanceSub, 'attendance_summary', function ($join) {
                $join->on('attendance_summary.user_id', '=', 'users.id');
            })
            ->leftJoin('attendance_logs as first_log', function ($join) {
                $join->on('first_log.user_id', '=', 'attendance_summary.user_id')
                    ->on('first_log.check_in', '=', 'attendance_summary.check_in');
            })
            ->leftJoin('attendance_logs as last_log', function ($join) {
                $join->on('last_log.user_id', '=', 'attendance_summary.user_id')
                    ->on('last_log.check_out', '=', 'attendance_summary.check_out');
            });

        // 4️⃣ Apply filters
        if ($request->filled('user')) {
            $query->where('users.employee_id', $request->input('user'));
        }

        if ($request->filled('branch')) {
            $query->where('branches.slug', $request->input('branch'));
        }

        if ($request->filled('department_slug')) {
            $query->where('departments.slug', $request->input('department_slug'));
        }

        if ($request->filled('designation_slug')) {
            $query->where('designations.slug', $request->input('designation_slug'));
        }

        if ($request->filled('status')) {
            $status = strtolower($request->input('status'));
            if ($status === 'present') {
                $query->whereNotNull('attendance_summary.check_in');
            } elseif ($status === 'absent') {
                $query->whereNull('attendance_summary.check_in');
            }
        }

        // 5️⃣ Order and paginate
        $attendance_summary = $query
            ->orderBy('attendance_summary.attendance_date', 'desc')
            ->paginate(100);

        return view('admin.attendance.attendance_logs', compact('attendance_summary'));
    }


    public function punchHistory()
    {
        $user_id = request()->get('user_id');
        $attendance_date = request()->get('attendance_date');
        if (!$user_id || !$attendance_date) {
            return response()->json(['error' => 'Employee and date are required.'], 400);
        }
        $punch_history = AttendanceLog::whereDate('check_in', $attendance_date)->where("user_id", $user_id)->select('id', 'user_id', 'check_in', 'check_out', 'client_ip', 'total_minutes')->get();
        $totalMinutes = $punch_history->sum('total_minutes');
        $totalHours   = round($totalMinutes / 60, 2);
        $html = view('admin.attendance.user_punch_history', compact('punch_history', 'totalHours'))->render();
        return response()->json([
            'title' => 'Punch history for ' . userNameById($user_id) . ' on ' . dateFormat($attendance_date, 'd M, y'),
            'html' => $html
        ]);
    }

    public function editAttendance($id) {
        $attendance = AttendanceLog::findOrFail(encrypt_decrypt($id,'decrypt'));
        $route = route('admin.update-attendance',$attendance->id);
        return view('admin.attendance.edit_attendance',compact('attendance','route'));
    }
}
