@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3 class="mb-3">Detail Absensi Tanggal {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>

  @if($attendance)
    <ul class="list-group">
      <li class="list-group-item"><strong>Status:</strong> {{ ucfirst($attendance->status) }}</li>
      <li class="list-group-item"><strong>Jam Masuk:</strong> {{ $attendance->check_in ?? '-' }}</li>
      <li class="list-group-item"><strong>Jam Keluar:</strong> {{ $attendance->check_out ?? '-' }}</li>
      <li class="list-group-item"><strong>Keterangan:</strong> {{ $attendance->keterangan ?? '-' }}</li>
      <li class="list-group-item">
        <strong>Bukti:</strong>
        @if($attendance->bukti)
          <a href="{{ asset('storage/' . $attendance->bukti) }}" target="_blank">Lihat Bukti</a>
        @else
          -
        @endif
      </li>
    </ul>
  @else
    <div class="alert alert-warning mt-3">
      Tidak ada data absensi pada tanggal ini.
    </div>
  @endif

  <a href="{{ route('user.attendance.history') }}" class="btn btn-secondary mt-4">← Kembali</a>
</div>
@endsection
