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
        // 1️⃣ Date range
        $startDate = $request->input('start_date')
            ? Carbon::parse($request->input('start_date'))->startOfDay()
            : Carbon::today()->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::parse($request->input('end_date'))->endOfDay()
            : $startDate->copy()->endOfDay();

        // 2️⃣ Subquery: last log of the day per user
        $attendanceSub = DB::table('attendance_logs as al')
            ->select(
                'al.id as last_log_id',
                'al.user_id',
                DB::raw('DATE(al.check_in) as attendance_date'),
                'al.check_in',
                'al.check_out',
                'al.total_minutes'
            )
            ->whereBetween('al.check_in', [$startDate, $endDate])
            ->whereRaw('al.check_in = (
            SELECT MAX(a2.check_in)
            FROM attendance_logs a2
            WHERE a2.user_id = al.user_id
            AND DATE(a2.check_in) = DATE(al.check_in)
        )');

        // 3️⃣ Main query: join summarized attendance with users + related tables
        $query = User::select([
            'users.id as user_id',
            'users.employee_id',
            'users.name as employee_name',
            'departments.name as department_name',
            'designations.name as designation_name',
            'branches.name as branch_name',
            'attendance_summary.attendance_date',
            'attendance_summary.check_in',
            'attendance_summary.check_out',
            'attendance_summary.total_minutes',
            'attendance_summary.last_log_id',
            DB::raw("ROUND(attendance_summary.total_minutes / 60, 2) as working_hours"),
            DB::raw("CASE WHEN attendance_summary.check_in IS NOT NULL THEN 'present' ELSE 'absent' END as status")
        ])
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->leftJoin('designations', 'users.designation_id', '=', 'designations.id')
            ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
            ->leftJoinSub($attendanceSub, 'attendance_summary', function ($join) {
                $join->on('attendance_summary.user_id', '=', 'users.id');
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

        // 5️⃣ Order + paginate
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
        $punch_history = AttendanceLog::whereDate('check_in', $attendance_date)->where("user_id", $user_id)->select('id', 'user_id', 'check_in', 'check_out', 'client_ip', 'total_minutes')->latest()->get();
        $totalMinutes = $punch_history->sum('total_minutes');
        $totalHours   = round($totalMinutes / 60, 2);
        $html = view('admin.attendance.user_punch_history', compact('punch_history', 'totalHours'))->render();
        return response()->json([
            'title' => 'Punch history for ' . userNameById($user_id) . ' on ' . dateFormat($attendance_date, 'd M, y'),
            'html' => $html
        ]);
    }

    public function editAttendance($id)
    {
        $attendance = AttendanceLog::whereNotNull('check_in')->whereNull('check_out')->findOrFail(encrypt_decrypt($id, 'decrypt'));
        $route = route('admin.update-attendance', $attendance->id);
        return view('admin.attendance.edit_attendance', compact('attendance', 'route'));
    }

    public function updateAttendance(Request $request, $id)
    {
        $log = AttendanceLog::findOrFail($id);

        $this->validate($request, [
            'check_out' => [
                'required',
                'date', // make sure it's a valid date
                function ($attribute, $value, $fail) use ($log) {
                    if (strtotime($value) <= strtotime($log->check_in)) {
                        $fail('Check-out time must be greater than check-in time.');
                    }
                }
            ],
        ]);

        $log->check_out = $request->input('check_out');
        // Update total_minutes if you have that field
        $log->total_minutes = ceil((strtotime($log->check_out) - strtotime($log->check_in)) / 60);
        $log->updated_by = Auth::id();
        $log->save();

        return redirect()->route('admin.attendance-logs')->with(infoMessage());
    }
}
