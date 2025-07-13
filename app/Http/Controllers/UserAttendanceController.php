<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Barcode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserAttendanceController extends Controller
{
    public function history(Request $request)
    {
        $month = $request->input('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
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

        return view('user.scan', compact('attendance', 'today')); // <== ini penting
    }

    public function store(Request $request)
    {
        $request->validate([
            'barcode_value' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $barcode = Barcode::where('value', $request->barcode_value)->first();

        if (!$barcode) {
            return back()->with('error', 'Barcode tidak dikenali.');
        }

        $distance = $this->calculateDistance($barcode->latitude, $barcode->longitude, $request->latitude, $request->longitude);
        if ($distance > $barcode->radius) {
            return back()->with('error', 'Lokasi terlalu jauh dari barcode.');
        }

        $today = now()->toDateString();
        $existing = Attendance::where('user_id', Auth::id())->where('date', $today)->first();
        if ($existing) {
            return back()->with('error', 'Kamu sudah absen hari ini.');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $today,
            'check_in' => now()->toTimeString(),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'present',
        ]);

        return back()->with('success', 'Absen berhasil.');
    }

    public function checkout(Request $request)
    {
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Kamu belum absen hari ini.');
        }

        if ($attendance->check_out) {
            return back()->with('error', 'Sudah checkout.');
        }

        $attendance->update([
            'check_out' => now()->toTimeString(),
        ]);

        return back()->with('success', 'Checkout berhasil.');
    }

    public function formIzin()
    {
        return view('user.izin-form');
    }

    public function submitIzin(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:date',
            'status' => 'required|in:sick,leave,absent',
            'keterangan' => 'required|string|max:255',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // Upload bukti
        $filename = null;
        if ($request->hasFile('bukti')) {
            $filename = $request->file('bukti')->store('bukti_izin', 'public');
        }

        Attendance::create([
            'user_id' => Auth::id(),
            'date' => $request->date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'keterangan' => $request->keterangan,
            'bukti' => $filename,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'Pengajuan izin/sakit berhasil dikirim.');
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
    public function showDetail($date)
    {
        $attendance = Attendance::where('user_id', Auth::id())
            ->where('date', $date)
            ->first();

        return view('user.attendance.detail', compact('attendance', 'date'));
    }

}
