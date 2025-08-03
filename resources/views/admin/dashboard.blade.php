@extends('layouts.app')

@section('title', 'Dasbor Admin')

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
      <h3 class="mb-2 mb-md-0">Rekap Absensi Hari Ini</h3>
      <h5 class="text-muted">Jumlah Karyawan: {{ $employeeCount }}</h5>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4">
      @php
        $cards = [
          ['label' => 'Hadir', 'count' => $presentCount, 'color' => 'success'],
          ['label' => 'Terlambat', 'count' => $lateCount, 'color' => 'warning'],
          ['label' => 'Izin / Cuti', 'count' => $excusedCount, 'color' => 'primary'],
          ['label' => 'Sakit', 'count' => $sickCount, 'color' => 'info'],
          ['label' => 'Tidak Hadir', 'count' => $absentCount, 'color' => 'danger'],
        ];
      @endphp

      @foreach ($cards as $data)
        <div class="col-6 col-md-4 col-lg-2">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
              <div class="fs-4 fw-bold text-{{ $data['color'] }}">{{ $data['count'] }}</div>
              <div class="small text-muted">{{ $data['label'] }}</div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Tabel Absensi --}}
    <div class="card shadow-sm">
      <div class="card-header">
        <h5 class="mb-0">Detail Absensi Hari Ini ({{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }})</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-striped table-vcenter card-table">
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
                      'leave', 'excused' => 'primary',
                      'sick' => 'info',
                      default => 'danger'
                    };
                  @endphp
                  <span class="badge bg-{{ $statusColor }}">{{ ucfirst($item->status) }}</span>
                </td>
                <td>{{ $item->check_in ?? '-' }}</td>
                <td>{{ $item->check_out ?? '-' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data absensi hari ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

{{-- Auto Refresh Tiap 60 detik --}}
<script>
  setTimeout(() => window.location.reload(), 60000);
</script>
@endsection
