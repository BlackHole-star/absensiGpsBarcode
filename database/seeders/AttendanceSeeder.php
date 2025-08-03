<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $months = [5, 6, 7]; // Mei, Juni, Juli
        $year = 2025;

        foreach ($users as $user) {
            foreach ($months as $month) {
                $start = Carbon::createFromDate($year, $month, 1);
                $end = $start->copy()->endOfMonth();

                for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                    if ($date->isWeekend()) continue; // skip Sabtu-Minggu

                    $rand = rand(1, 100);
                    $status = 'present';
                    $time_in = '09:00:00';
                    $time_out = '17:00:00';
                    $note = null;

                    if ($rand <= 2) {
                        $status = 'absent';
                        $time_in = null;
                        $time_out = null;
                    } elseif ($rand <= 5) {
                        $status = 'sick';
                        $time_in = null;
                        $time_out = null;
                        $note = 'Sakit';
                    } elseif ($rand <= 10) {
                        $status = 'excused';
                        $time_in = null;
                        $time_out = null;
                        $note = 'Izin pribadi';
                    } elseif ($rand <= 20) {
                        $status = 'late';
                        $minute = rand(2, 5);
                        $time_in = "09:0{$minute}:00";
                        $time_out = '17:00:00';
                        $note = 'Terlambat';
                    }

                    Attendance::create([
                        'user_id' => $user->id,
                        'barcode_id' => null,
                        'date' => $date->toDateString(),
                        'time_in' => $time_in,
                        'time_out' => $time_out,
                        'status' => $status,
                        'note' => $note,
                        'attachment' => null,
                    ]);
                }
            }
        }
    }
}
