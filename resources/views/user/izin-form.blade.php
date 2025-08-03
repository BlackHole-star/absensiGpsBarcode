@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Pengajuan Izin / Sakit</h3>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  {{-- Tombol Kembali --}}
  <div class="mb-3">
    <a href="{{ route('user.absen.scan') }}" class="btn btn-outline-secondary">
      ← Kembali
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="{{ route('user.absen.izin.submit') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
          <label for="status" class="form-label">Status</label>
          <select name="status" id="status" class="form-select" required>
            <option value="">-- Pilih --</option>
            <option value="excused">Izin</option>
            <option value="sick">Sakit</option>
          </select>
        </div>

        <div class="mb-3">
          <label for="from" class="form-label">Tanggal Mulai</label>
          <input type="date" name="from" id="from" class="form-control" min="{{ date('Y-m-d') }}" required>
        </div>

        <div class="mb-3">
          <label for="to" class="form-label">Tanggal Berakhir <small class="text-muted">(Opsional)</small></label>
          <input type="date" name="to" id="to" class="form-control" min="{{ date('Y-m-d') }}">
        </div>

        <div class="mb-3">
          <label for="note" class="form-label">Keterangan</label>
          <textarea name="note" id="note" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
          <label for="attachment" class="form-label">Lampiran Bukti <small class="text-muted">(Opsional)</small></label>
          <input type="file" name="attachment" id="attachment" class="form-control" onchange="previewImage(event)">
          <div class="mt-3 d-none" id="preview">
            <img id="preview-img" class="img-fluid rounded border" style="max-height: 250px;">
          </div>
        </div>

        {{-- Lokasi GPS --}}
        {{-- <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude"> --}}

        <div class="mt-4 text-end">
          <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- Script --}}
<script>
  // Lokasi otomatis
  // if (navigator.geolocation) {
  //   navigator.geolocation.getCurrentPosition(function (pos) {
  //     document.getElementById('latitude').value = pos.coords.latitude;
  //     document.getElementById('longitude').value = pos.coords.longitude;
  //   }, function () {
  //     alert('Gagal mendeteksi lokasi. Aktifkan izin lokasi browser.');
  //   });
  // }

  // Preview lampiran gambar
  function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');
    const img = document.getElementById('preview-img');
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        img.src = e.target.result;
        preview.classList.remove('d-none');
      };
      reader.readAsDataURL(file);
    }
  }
</script>
@endsection
