<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use App\Models\Barcode;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();
        $userId = Auth::id();

        // Auto-mark Alpha jika belum absen dan lewat batas waktu absen masuk
        $this->autoAlphaCheck($userId, $today);

        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();

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

        $userId = Auth::id();
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $userId)->where('date', $today)->first();
        $now = now();

        $jamMasukMulai = Carbon::createFromTime(9, 0);
        $jamMasukAkhir = Carbon::createFromTime(9, 5);
        $jamTelatMulai = Carbon::createFromTime(9, 2);

        $jamKeluarMulai = Carbon::createFromTime(9, 10);
        $jamKeluarAkhir = Carbon::createFromTime(9, 15);

        // Absen masuk
        if (!$attendance) {
            if ($now->lt($jamMasukMulai) || $now->gt($jamMasukAkhir)) {
                return response()->json(['message' => 'Waktu absen masuk di luar jam yang diperbolehkan.'], 400);
            }

            $status = $now->between($jamTelatMulai, $jamMasukAkhir) ? 'late' : 'present';

            Attendance::create([
                'user_id' => $userId,
                'barcode_id' => $barcode->id,
                'date' => $today,
                'time_in' => $now->toTimeString(),
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'status' => $status,
            ]);

            return response()->json(['message' => 'Absen masuk berhasil.']);
        }

        // Absen keluar
        if ($attendance->time_out) {
            return response()->json(['message' => 'Sudah melakukan absen keluar.'], 400);
        }

        if ($now->lt($jamKeluarMulai) || $now->gt($jamKeluarAkhir)) {
            return response()->json(['message' => 'Waktu absen pulang di luar jam yang diperbolehkan.'], 400);
        }

        $attendance->update([
            'time_out' => $now->toTimeString(),
        ]);

        return response()->json(['message' => 'Absen keluar berhasil.']);
    }

    private function autoAlphaCheck($userId, $date)
    {
        $existing = Attendance::where('user_id', $userId)->where('date', $date)->first();
        $jamMasukAkhir = Carbon::createFromTime(9, 5);

        if (!$existing && now()->gt($jamMasukAkhir)) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $date,
                'status' => 'absent',
            ]);
        }

        if ($existing && !$existing->time_out && now()->gt(Carbon::createFromTime(9, 15))) {
            $existing->update([
                'status' => 'absent',
            ]);
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}