<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $presentCount  = Attendance::where('date', $date)->where('status', 'present')->count();
        $lateCount     = Attendance::where('date', $date)->where('status', 'late')->count();
        $excusedCount  = Attendance::where('date', $date)->where('status', 'leave')->count();
        $sickCount     = Attendance::where('date', $date)->where('status', 'sick')->count();
        $absentCount   = Attendance::where('date', $date)->where('status', 'absent')->count();

        $rekap = Attendance::with('user')->where('date', $date)->get();
        $employeeCount = \App\Models\User::where('role', 'user')->count();

        return view('admin.dashboard', compact(
            'presentCount',
            'lateCount',
            'excusedCount',
            'sickCount',
            'absentCount',
            'rekap',
            'employeeCount',
            'date'
        ));
    }
}
