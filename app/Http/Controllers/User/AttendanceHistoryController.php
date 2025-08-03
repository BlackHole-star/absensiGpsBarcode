<?php 

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceHistoryController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [$start, $end])
            ->get();

        return view('user.attendance.attendance-history', compact('attendances'));
    }

    public function show($date)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $date)
            ->first();

        return view('user.attendance.detail', compact('attendance', 'date'));
    }
}
