@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
    <h3 class="mb-2 mb-md-0">Absensi Hari Ini</h3>
    <h5 class="text-muted">Jumlah Karyawan: {{ $employeeCount ?? 0 }}</h5>
  </div>

  {{-- Kartu Statistik --}}
  <div class="row g-3 mb-4">
    @php
      $cards = [
        'present' => ['label' => 'Hadir', 'count' => $presentCount, 'color' => 'green', 'sub' => "Terlambat: $lateCount"],
        'excused' => ['label' => 'Izin', 'count' => $excusedCount, 'color' => 'blue', 'sub' => 'Izin/Cuti'],
        'sick' => ['label' => 'Sakit', 'count' => $sickCount, 'color' => 'purple', 'sub' => '-'],
        'absent' => ['label' => 'Tidak Hadir', 'count' => $absentCount, 'color' => 'red', 'sub' => 'Tidak/Belum Hadir'],
      ];
    @endphp

    @foreach ($cards as $key => $data)
    <div class="col-6 col-md-3">
      <div class="card shadow-sm h-100 border-0 bg-{{ $data['color'] }}-lt">
        <div class="card-body text-{{ $data['color'] }}">
          <div class="fs-4 fw-bold">{{ $data['label'] }}: {{ $data['count'] }}</div>
          @if($data['sub'] !== '-')
            <div class="small">{{ $data['sub'] }}</div>
          @endif
        </div>
      </div>
    </div>
    @endforeach
  </div>

  {{-- Filter Tanggal --}}
  <form method="GET" action="{{ route('admin.dashboard') }}" class="mb-4">
    <div class="row g-2">
      <div class="col-md-4">
        <input type="date" name="date" class="form-control" value="{{ $date }}">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
      </div>
    </div>
  </form>

  {{-- Tabel Rekap --}}
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h4 class="mb-0">Rekap Absensi ({{ $date }})</h4>
      <div>
        <a href="{{ route('admin.export.excel') }}" class="btn btn-success btn-sm">Export Excel</a>
        <a href="{{ route('admin.export.pdf') }}" class="btn btn-danger btn-sm">Export PDF</a>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-vcenter card-table table-striped">
        <thead class="bg-light">
          <tr>
            <th>Nama</th>
            <th>Status</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rekap as $item)
          <tr>
            <td>{{ $item->user->name ?? '-' }}</td>
            <td>
              @php
                $statusColor = match($item->status) {
                  'present' => 'success',
                  'late' => 'warning',
                  'excused', 'leave' => 'primary',
                  'sick' => 'info',
                  default => 'danger'
                };
              @endphp
              <span class="badge bg-{{ $statusColor }}">
                {{ ucfirst($item->status) }}
              </span>
            </td>
            <td>{{ $item->check_in ?? '-' }}</td>
            <td>{{ $item->check_out ?? '-' }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted">Belum ada data</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- Auto Refresh --}}
<script>
  setTimeout(() => window.location.reload(), 60000);
</script>
@endsection
