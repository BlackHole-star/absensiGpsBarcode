@extends('layouts.app')

@section('title', 'Scan Absen')

@section('content')
<div class="container py-4">
  <h3 class="mb-4">Scan QR & Lokasi</h3>

  <div class="card shadow-sm p-4">
    {{-- Header --}}
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Waktu Kerja</label>
        <input type="text" class="form-control" value="09:00 - 17:00" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Tanggal</label>
        <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($today ?? now())->format('d/m/Y') }}" readonly>
        <small class="text-muted mt-1 d-block">Lokasi Anda: <span id="currentLocation">Mendeteksi...</span></small>
      </div>
    </div>

    {{-- QR & Map --}}
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">QR Scanner</label>
        <div id="qr-reader" class="rounded border" style="width: 100%; height: 250px; background: #000;"></div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Peta Lokasi</label>
        <div id="map" class="rounded border" style="width: 100%; height: 250px;"></div>
      </div>
    </div>

    {{-- Status --}}
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card text-white text-center p-3 h-100" style="background-color: #007bff;">
          <div class="text-sm">Absen Masuk</div>
          <div class="fw-bold text-lg">{{ $attendance?->check_in ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-dark text-center p-3 h-100" style="background-color: #ffc107;">
          <div class="text-sm">Absen Keluar</div>
          <div class="fw-bold text-lg">{{ $attendance?->check_out ?? '-' }}</div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card text-white text-center p-3 h-100" style="background-color: #6f42c1;">
          <div class="text-sm">Koordinat Absen</div>
          <div class="fw-bold text-lg">
            {{ $attendance?->latitude ? $attendance->latitude . ', ' . $attendance->longitude : '-' }}
          </div>
        </div>
      </div>
    </div>

    {{-- Tombol --}}
    <div class="d-flex justify-content-between">
      <a href="{{ route('user.absen.izin') }}" class="btn btn-warning w-100 me-2 py-2">Ajukan Izin</a>
      <a href="{{ route('user.attendance.history') }}" class="btn btn-primary w-100 ms-2 py-2">Riwayat Absen</a>
    </div>
  </div>
</div>

{{-- Script --}}
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  let lat = null, lon = null;

  navigator.geolocation.getCurrentPosition(function(position) {
    lat = position.coords.latitude;
    lon = position.coords.longitude;

    document.getElementById('currentLocation').textContent = `${lat.toFixed(5)}, ${lon.toFixed(5)}`;

    const map = L.map('map').setView([lat, lon], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    L.marker([lat, lon]).addTo(map);
  }, function(err) {
    alert("Gagal mengakses lokasi. Izin ditolak.");
  });

  const qrScanner = new Html5Qrcode("qr-reader");

  Html5Qrcode.getCameras().then(devices => {
    if (devices && devices.length > 0) {
      const cameraId = devices[0].id;

      qrScanner.start(
        cameraId,
        {
          fps: 10,
          qrbox: function(w, h) {
            const size = Math.min(w, h) * 0.6;
            return { width: size, height: size };
          }
        },
        function (decodedText) {
          qrScanner.stop().then(() => {
            fetch("{{ route('user.absen.store') }}", {
              method: "POST",
              headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Content-Type': 'application/json'
              },
              body: JSON.stringify({
                barcode_value: decodedText,
                latitude: lat,
                longitude: lon
              })
            })
            .then(res => res.json())
            .then(data => {
              alert(data.message || "Absen berhasil");
              setTimeout(() => location.reload(), 1000);
            })
            .catch(err => alert("Gagal mengirim data absen"));
          });
        },
        function (errorMessage) {
          // silent
        }
      );
    } else {
      alert("Tidak ada kamera tersedia.");
    }
  }).catch(err => {
    alert("Gagal akses kamera: " + err);
  });
</script>
@endsection
