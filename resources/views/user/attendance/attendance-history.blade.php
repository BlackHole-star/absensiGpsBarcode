@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="mb-3">
  <a href="{{ route('user.absen.scan') }}" class="btn btn-outline-secondary">
    ← Kembali
  </a>
</div>
<h3 class="mb-4">Riwayat Absensi</h3>

  {{-- Filter Bulan --}}
  <form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
      <input type="month" name="month" class="form-control" value="{{ request('month', now()->format('Y-m')) }}">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
    </div>
  </form>

  @php
    use Carbon\Carbon;

    $selectedMonth = request('month', now()->format('Y-m'));
    $start = Carbon::parse($selectedMonth . '-01');
    $end = $start->copy()->endOfMonth();
    $attMap = collect($attendances)->keyBy('date');

    $presentCount = $lateCount = $excusedCount = $sickCount = $absentCount = 0;
    $today = Carbon::now();
  @endphp

  <div class="table-responsive">
    <table class="table table-bordered text-center align-middle" style="min-width: 500px;">
      <thead class="table-light">
        <tr>
          @foreach (['M', 'S', 'S', 'R', 'K', 'J', 'S'] as $day)
            <th>{{ $day }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @php
          $current = $start->copy()->startOfWeek(Carbon::MONDAY);
        @endphp
        @while ($current <= $end)
          <tr>
            @for ($i = 0; $i < 7; $i++)
              @php
                $dateStr = $current->format('Y-m-d');
                $isInMonth = $current->month == $start->month;
                $att = $attMap[$dateStr] ?? null;
                $status = $att->status ?? ($current->isPast() && $isInMonth ? 'absent' : '-');

                switch ($status) {
                  case 'present':
                    $short = 'H';
                    $class = 'bg-success text-white';
                    $presentCount++;
                    break;
                  case 'late':
                    $short = 'T';
                    $class = 'bg-warning text-dark';
                    $lateCount++;
                    break;
                  case 'leave':
                  case 'excused':
                    $short = 'I';
                    $class = 'bg-primary text-white';
                    $excusedCount++;
                    break;
                  case 'sick':
                    $short = 'S';
                    $class = 'bg-purple text-white';
                    $sickCount++;
                    break;
                  case 'absent':
                    $short = 'A';
                    $class = 'bg-danger text-white';
                    $absentCount++;
                    break;
                  default:
                    $short = '-';
                    $class = 'bg-secondary text-white';
                }

                $url = route('user.attendance.detail', ['date' => $dateStr]);
              @endphp

              @if ($isInMonth)
                <td class="{{ $class }}">
                  <a href="{{ $url }}" class="text-decoration-none text-white d-block">
                    <div>{{ $current->format('d') }}</div>
                    <small>{{ $short }}</small>
                  </a>
                </td>
              @else
                <td class="bg-light"></td>
              @endif

              @php $current->addDay(); @endphp
            @endfor
          </tr>
        @endwhile
      </tbody>
    </table>
  </div>

  {{-- Statistik --}}
  <div class="row mt-4 g-2">
    <div class="col-md-3">
      <div class="bg-success text-white p-3 rounded">
        <div class="fw-bold">Hadir: {{ $presentCount + $lateCount }}</div>
        <small>Terlambat: {{ $lateCount }}</small>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-primary text-white p-3 rounded">
        <div class="fw-bold">Izin: {{ $excusedCount }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-purple text-white p-3 rounded">
        <div class="fw-bold">Sakit: {{ $sickCount }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="bg-danger text-white p-3 rounded">
        <div class="fw-bold">Absen: {{ $absentCount }}</div>
      </div>
    </div>
  </div>
</div>

<style>
  .bg-purple {
    background-color: #6f42c1;
  }
</style>
@endsection
