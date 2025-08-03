<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function history(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $attendances = Attendance::where('user_id', Auth::id())
            ->whereBetween('date', [$start, $end])
            ->get();

        return view('user.attendance.attendance-history', compact('attendances'));
    }

    public function scanForm()
    {
        $today = now()->toDateString();
        $userId = Auth::id();

        $attendance = Attendance::where('user_id', $userId)
            ->where('date', $today)
            ->first();

        return view('user.scan', compact('attendance', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode_value' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $barcode = Barcode::where('value', $request->barcode_value)->first();

        if (!$barcode) {
            return response()->json(['message' => 'Barcode tidak dikenali.'], 400);
        }

        $distance = $this->calculateDistance($barcode->latitude, $barcode->longitude, $request->latitude, $request->longitude);
        if ($distance > $barcode->radius) {
            return response()->json(['message' => 'Lokasi terlalu jauh dari barcode.'], 400);
        }

        $today = now()->toDateString();
        $userId = Auth::id();
        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();

        if ($attendance) {
            if ($attendance->time_in && !$attendance->time_out) {
                // Sudah absen masuk tapi belum keluar → proses absen keluar
                $attendance->update([
                    'time_out' => now()->toTimeString(),
                ]);

                return response()->json(['message' => 'Absen keluar berhasil.']);
            } elseif ($attendance->time_in && $attendance->time_out) {
                // Sudah absen masuk dan keluar
                return response()->json(['message' => 'Kamu sudah absen masuk dan keluar hari ini.'], 400);
            } else {
                // Aneh, tapi fallback
                return response()->json(['message' => 'Absen sudah tercatat.'], 400);
            }
        }

        // Belum ada data → proses absen masuk
        $createdTime = Carbon::parse($barcode->created_at);
        $isLate = now()->diffInMinutes($createdTime) > 30;

        Attendance::create([
            'user_id' => $userId,
            'barcode_id' => $barcode->id,
            'date' => $today,
            'time_in' => now()->toTimeString(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => $isLate ? 'late' : 'present',
        ]);

        return response()->json(['message' => 'Absen masuk berhasil.']);
    }

    public function checkout(Request $request)
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Kamu belum absen masuk.');
        }

        if ($attendance->time_out) {
            return redirect()->back()->with('error', 'Sudah checkout.');
        }

        $attendance->update([
            'time_out' => now()->toTimeString(),
        ]);

        return redirect()->back()->with('success', 'Checkout berhasil.');
    }

    public function formIzin()
    {
        return view('user.izin-form');
    }

    public function submitIzin(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'nullable|date|after_or_equal:from',
            'status' => 'required|in:sick,excused',
            'note' => 'required|string|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $filename = null;
        if ($request->hasFile('attachment')) {
            $filename = $request->file('attachment')->store('bukti_izin', 'public');
        }

        $dates = $request->to
            ? CarbonPeriod::create($request->from, $request->to)
            : [Carbon::parse($request->from)];

        foreach ($dates as $day) {
            Attendance::create([
                'user_id' => Auth::id(),
                'date' => Carbon::parse($day)->toDateString(),
                'status' => $request->status,
                'note' => $request->note,
                'attachment' => $filename,
            ]);
        }

        return redirect()->route('user.absen.scan')->with('success', 'Pengajuan izin/sakit berhasil dikirim.');
    }

    public function showDetail($date)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $date)
            ->first();

        return view('user.attendance.detail', compact('attendance', 'date'));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
