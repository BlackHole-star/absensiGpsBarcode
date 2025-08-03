<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function rekap(Request $request)
    {
        $today = Carbon::today();
        $now = Carbon::now();
        $date = $request->input('date');
        $week = $request->input('week');
        $month = $request->input('month');
        $year = $request->input('year');

        $type = 'daily'; // default
        $start = $today->copy()->startOfDay();
        $end = $today->copy()->endOfDay();

        // urut: tahun → bulan → minggu → tanggal
        if ($year && !$month && !$week && !$date) {
            $type = 'yearly';
            $start = Carbon::createFromDate($year, 1, 1)->startOfDay();
            $end = Carbon::createFromDate($year, 12, 31)->endOfDay();
        } elseif ($month && !$week && !$date) {
            $type = 'monthly';
            try {
                $start = Carbon::parse($month . '-01')->startOfMonth();
                $end = Carbon::parse($month . '-01')->endOfMonth();
            } catch (\Exception $e) {
                $start = $today->copy();
                $end = $today->copy();
            }
        } elseif ($week && !$date) {
            if (preg_match('/^(\d{4})-W(\d{2})$/', $week, $match)) {
                $weekYear = (int) $match[1];
                $weekNum = (int) $match[2];
                $maxWeeks = Carbon::parse("$weekYear-12-28")->isoFormat('W');

                if ($weekNum <= $maxWeeks) {
                    $weekCarbon = Carbon::now()->setISODate($weekYear, $weekNum);
                    $type = 'weekly';
                    $start = $weekCarbon->copy()->startOfWeek();
                    $end = $weekCarbon->copy()->endOfWeek();
                }
            }
        } elseif ($date) {
            $type = 'daily';
            try {
                $start = Carbon::parse($date)->startOfDay();
                $end = Carbon::parse($date)->endOfDay();
            } catch (\Exception $e) {
                $start = $today->copy();
                $end = $today->copy();
            }
        }

        // Batasi end date gak boleh lewat hari ini
        if ($start->gt($now)) $start = $now->copy()->startOfDay();
        if ($end->gt($now)) $end = $now->copy()->endOfDay();

        $users = User::where('role', 'user')->orderBy('name')->get();
        $attendances = Attendance::whereBetween('date', [$start, $end])->get()->groupBy('user_id');
        $rekapData = [];

        if ($type === 'yearly') {
            foreach ($users as $user) {
                $summary = ['H' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0];
                foreach ($attendances->get($user->id, []) as $record) {
                    $status = match ($record->status) {
                        'present' => 'H',
                        'late' => 'T',
                        'excused', 'leave' => 'I',
                        'sick' => 'S',
                        'absent' => 'A',
                        default => 'A',
                    };
                    $summary[$status] = ($summary[$status] ?? 0) + 1;
                }

                $rekapData[] = [
                    'name' => $user->name,
                    'summary' => $summary,
                ];
            }

            $dateRange = [];
        } else {
            $dateRange = [];
            for ($d = $start->copy(); $d <= $end; $d->addDay()) {
                $dateRange[] = $d->format('Y-m-d');
            }

            foreach ($users as $user) {
                $data = [
                    'name' => $user->name,
                    'statuses' => [],
                    'summary' => ['H' => 0, 'T' => 0, 'I' => 0, 'S' => 0, 'A' => 0],
                ];

                foreach ($dateRange as $tanggal) {
                    $record = $attendances->get($user->id)?->firstWhere('date', $tanggal);
                    $carbonDate = Carbon::parse($tanggal);

                    if (in_array($carbonDate->dayOfWeekIso, [6, 7])) {
                        $status = '-';
                    } elseif (!$record) {
                        $status = 'A';
                    } else {
                        $status = match ($record->status) {
                            'present' => 'H',
                            'late' => 'T',
                            'excused', 'leave' => 'I',
                            'sick' => 'S',
                            'absent' => 'A',
                            default => 'A',
                        };
                    }

                    $data['statuses'][] = $status;
                    if (isset($data['summary'][$status])) {
                        $data['summary'][$status]++;
                    }
                }

                $rekapData[] = $data;
            }
        }

        return view('admin.rekap', compact('type', 'dateRange', 'rekapData', 'date', 'week', 'month', 'year'));
    }
}
