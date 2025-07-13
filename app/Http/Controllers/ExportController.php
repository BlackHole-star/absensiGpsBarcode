<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use PDF;

class ExportController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new AttendanceExport, 'absensi.xlsx');
    }

    public function exportPDF()
    {
        $attendances = Attendance::with('user')->latest()->get();
        $pdf = PDF::loadView('admin.exports.attendance-pdf', compact('attendances'));
        return $pdf->download('absensi.pdf');
    }
}
