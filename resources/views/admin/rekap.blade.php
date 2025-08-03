@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 rounded shadow-sm">

    @php
      $startDate = $dateRange[0] ?? null;
      $endDate = end($dateRange) ?: null;
    @endphp

    <h3 class="mb-4 fw-semibold text-dark">
      Rekap Absensi
      @if($type === 'monthly' && $month)
        Bulanan — {{ \Carbon\Carbon::parse($month.'-01')->translatedFormat('F Y') }}
      @elseif($type === 'weekly' && $startDate && $endDate)
        Mingguan — {{ \Carbon\Carbon::parse($startDate)->format('d M') }} – {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
      @elseif($type === 'daily' && $date)
        Harian — {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
      @elseif($type === 'yearly' && $year)
        Tahunan — {{ $year }}
      @else
        Harian — {{ \Carbon\Carbon::today()->translatedFormat('d F Y') }}
      @endif
    </h3>

    {{-- Formulir Filter --}}
    <form method="GET" class="row row-cols-1 row-cols-md-auto g-3 align-items-end mb-4">
      <div class="col">
        <label for="year" class="form-label">Tahun</label>
        <input type="number" id="year" name="year" class="form-control" min="2021" max="{{ now()->year }}" placeholder="Contoh: 2025" value="{{ $year ?? '' }}">
      </div>

      <div class="col">
        <label for="month" class="form-label">Bulan</label>
        <input type="month" id="month" name="month" class="form-control" max="{{ now()->format('Y-m') }}" value="{{ $month ?? '' }}">
      </div>

      <div class="col">
        <label for="week" class="form-label">Minggu</label>
        <input type="week" id="week" name="week" class="form-control" max="{{ now()->format('o-\WW') }}" value="{{ $week ?? '' }}">
      </div>

      <div class="col">
        <label for="date" class="form-label">Tanggal</label>
        <input type="date" id="date" name="date" class="form-control" max="{{ now()->format('Y-m-d') }}" value="{{ $date ?? '' }}">
      </div>

      <div class="col">
        <button type="submit" class="btn btn-primary w-100">
          <svg xmlns="http://www.w3.org/2000/svg" class="me-1" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11 1.5a.5.5 0 0 1 .5.5v1H13a2 2 0 0 1 2 2v1h-1V5a1 1 0 0 0-1-1h-1.5v1a.5.5 0 0 1-1 0V4H7v1a.5.5 0 0 1-1 0V4H4.5a1 1 0 0 0-1 1v1H2V4a2 2 0 0 1 2-2h1.5V2a.5.5 0 0 1 1 0v.5h4V2a.5.5 0 0 1 .5-.5ZM1 8h14v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8Zm4 2.5a.5.5 0 0 0-1 0V12a.5.5 0 0 0 1 0v-1.5ZM8 10a.5.5 0 0 1 .5.5V12a.5.5 0 0 1-1 0v-1.5A.5.5 0 0 1 8 10Zm3.5.5a.5.5 0 0 0-1 0V12a.5.5 0 0 0 1 0v-1.5Z"/></svg>
          Tampilkan
        </button>
      </div>
    </form>

    {{-- Tabel Rekap --}}
    <div class="table-responsive">
      <table class="table table-bordered table-sm align-middle text-center">
        <thead class="table-light">
          <tr>
            <th>Nama</th>
            @if ($type !== 'yearly')
              @foreach ($dateRange as $d)
                <th>{{ \Carbon\Carbon::parse($d)->format('d/m') }}</th>
              @endforeach
            @endif
            <th>H</th><th>T</th><th>I</th><th>S</th><th>A</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($rekapData as $data)
            <tr>
              <td class="text-start">{{ $data['name'] }}</td>
              @if ($type !== 'yearly')
                @foreach ($data['statuses'] as $s)
                  @php
                    $bg = match($s) {
                      'H' => 'bg-success-subtle',
                      'T' => 'bg-warning-subtle',
                      'I' => 'bg-info-subtle',
                      'S' => 'bg-primary-subtle',
                      'A' => 'bg-danger-subtle',
                      default => '',
                    };
                  @endphp
                  <td class="{{ $bg }}">{{ $s }}</td>
                @endforeach
              @endif
              <td>{{ $data['summary']['H'] }}</td>
              <td>{{ $data['summary']['T'] }}</td>
              <td>{{ $data['summary']['I'] }}</td>
              <td>{{ $data['summary']['S'] }}</td>
              <td>{{ $data['summary']['A'] }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="{{ count($dateRange) + 6 }}" class="text-muted text-center">Tidak ada data tersedia.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>

{{-- Validasi Filter dengan JavaScript --}}
<script>
  const now = new Date();

  const yearInput = document.getElementById('year');
  const monthInput = document.getElementById('month');
  const weekInput = document.getElementById('week');
  const dateInput = document.getElementById('date');

  function pad(n) {
    return n < 10 ? '0' + n : n;
  }

  function getDateOfISOWeek(w, y) {
    const simple = new Date(y, 0, 1 + (w - 1) * 7);
    const dow = simple.getDay();
    const ISOweekStart = new Date(simple);
    if (dow <= 4)
      ISOweekStart.setDate(simple.getDate() - simple.getDay() + 1);
    else
      ISOweekStart.setDate(simple.getDate() + 8 - simple.getDay());
    return ISOweekStart;
  }

  function validateFilterLimits() {
    const year = parseInt(yearInput.value);
    const month = monthInput.value;
    const week = weekInput.value;
    const date = dateInput.value;
    const selectedDate = date ? new Date(date) : null;

    if (selectedDate && selectedDate > now) dateInput.value = '';

    if (year) {
      if (year > now.getFullYear()) yearInput.value = '';

      if (month) {
        const [mYear] = month.split('-');
        if (parseInt(mYear) !== year || new Date(month + "-01") > now) monthInput.value = '';
      }

      if (week) {
        const [wYear] = week.split('-W');
        const wDate = getDateOfISOWeek(parseInt(week.split('-W')[1]), parseInt(wYear));
        if (parseInt(wYear) !== year || wDate > now) weekInput.value = '';
      }

      if (selectedDate && selectedDate.getFullYear() !== year) dateInput.value = '';
    }

    if (month) {
      const [mYear, mMonth] = month.split('-');
      const monthDate = new Date(month + "-01");
      if (monthDate > now) monthInput.value = '';

      if (week) {
        const [wYear, wWeek] = week.split('-W');
        const weekStart = getDateOfISOWeek(parseInt(wWeek), parseInt(wYear));
        if (
          weekStart.getMonth() + 1 !== parseInt(mMonth) ||
          wYear !== mYear ||
          weekStart > now
        ) weekInput.value = '';
      }

      if (selectedDate) {
        if (
          selectedDate.getFullYear() !== parseInt(mYear) ||
          selectedDate.getMonth() + 1 !== parseInt(mMonth)
        ) dateInput.value = '';
      }
    }

    if (week && selectedDate) {
      const [wYear, wWeek] = week.split('-W');
      const weekStart = getDateOfISOWeek(parseInt(wWeek), parseInt(wYear));
      const weekEnd = new Date(weekStart);
      weekEnd.setDate(weekEnd.getDate() + 6);
      if (selectedDate < weekStart || selectedDate > weekEnd || selectedDate > now) {
        dateInput.value = '';
      }
    }
  }

  [yearInput, monthInput, weekInput, dateInput].forEach(input => {
    input?.addEventListener('change', validateFilterLimits);
  });
</script>
@endsection
