@extends('layouts.app')

@section('content')
  <h2>Rekap Absensi Tanggal {{ $date }}</h2>

  <form method="GET" action="{{ route('admin.rekap') }}" class="mb-3">
    <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
  </form>

  @php
    $statuses = ['present' => 'Hadir', 'sick' => 'Sakit', 'leave' => 'Izin', 'absent' => 'Tidak Hadir'];
  @endphp

  @foreach($statuses as $key => $label)
    <div class="card mb-3">
      <div class="card-header"><strong>{{ $label }}</strong></div>
      <div class="card-body">
        @forelse($rekap[$key] ?? [] as $absen)
          <li>{{ $absen->user->name }}</li>
        @empty
          <p class="text-muted">Tidak ada data</p>
        @endforelse
      </div>
    </div>
  @endforeach
@endsection
