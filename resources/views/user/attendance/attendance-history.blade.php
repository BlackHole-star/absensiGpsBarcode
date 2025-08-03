@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Riwayat Absensi</h3>

  <div class="mb-3">
    <a href="{{ route('user.absen.scan') }}" class="btn btn-outline-secondary">← Kembali</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">

      {{-- Filter Bulan --}}
      <form method="GET" class="row g-2 mb-4 align-items-end">
        <div class="col-md-4">
          <label for="month" class="form-label">Pilih Bulan</label>
          <input 
            type="month" 
            name="month" 
            id="month" 
            class="form-control"
            value="{{ request('month', now()->format('Y-m')) }}"
            max="{{ now()->format('Y-m') }}"
            required
          >
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
        $current = $start->copy()->startOfWeek(Carbon::MONDAY);
      @endphp

      <div class="table-responsive">
        <table class="table table-bordered text-center align-middle" style="min-width: 500px;">
          <thead class="table-light">
            <tr>
              @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                <th>{{ $day }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @while ($current <= $end)
              <tr>
                @for ($i = 0; $i < 7; $i++)
                  @php
                    $dateStr = $current->format('Y-m-d');
                    $dayOfWeek = $current->dayOfWeek;
                    $isInMonth = $current->month == $start->month;

                    $att = $attMap[$dateStr] ?? null;
                    $status = '-';
                    $short = '-';
                    $class = 'bg-secondary text-white';

                    if ($dayOfWeek != 0 && $dayOfWeek != 6 && $isInMonth) {
                      $status = $att->status ?? ($current->isPast() ? 'absent' : '-');

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
                    }

                    $isSunday = $dayOfWeek === 0;
                    $url = route('user.absen.history.detail', ['date' => $dateStr]);
                  @endphp

                  @if ($isInMonth)
                    <td class="{{ $class }}">
                      <a href="{{ $url }}" class="text-decoration-none d-block {{ $isSunday ? 'text-danger' : 'text-white' }}">
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
  </div>
</div>

<style>
  .bg-purple {
    background-color: #6f42c1;
  }
</style>
@endsection
