<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;

class AttendanceExport implements FromCollection
{
    public function collection()
    {
        return Attendance::with('user')->latest()->get()->map(function ($item) {
            return [
                'Nama' => $item->user->name,
                'Tanggal' => $item->date,
                'Check In' => $item->check_in,
                'Check Out' => $item->check_out,
                'Status' => $item->status,
                'Latitude' => $item->latitude,
                'Longitude' => $item->longitude,
            ];
        });
    }
}
