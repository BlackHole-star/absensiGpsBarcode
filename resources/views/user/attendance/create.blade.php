@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h2 class="mb-4">Scan Barcode & Lokasi</h2>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <form method="POST" action="{{ route('user.attendance.store') }}" id="absen-form">
    @csrf

    <input type="hidden" name="barcode_value" id="barcode_value" required>
    <input type="hidden" name="latitude" id="latitude" required>
    <input type="hidden" name="longitude" id="longitude" required>

    <div class="row">
      <div class="col-md-6 mb-3">
        <div id="reader" style="width: 100%;"></div>
        <p class="text-muted mt-2"><strong>Barcode:</strong> <span id="scanned-code">Belum scan</span></p>
      </div>
      <div class="col-md-6 mb-3">
        <div id="map" style="width: 100%; height: 300px;"></div>
        <p class="text-muted mt-2"><strong>Lokasi:</strong> <span id="lokasi-coord">Mengambil lokasi...</span></p>
      </div>
    </div>

    <button type="submit" class="btn btn-primary w-100">Submit Absensi</button>
  </form>
</div>
@endsection

@push('scripts')
<!-- QR SCANNER -->
<script src="https://unpkg.com/html5-qrcode"></script>

<!-- LEAFLET.JS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
  // Start Scanner
  const qrScanner = new Html5Qrcode("reader");
  qrScanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    qrCodeMessage => {
      document.getElementById('barcode_value').value = qrCodeMessage;
      document.getElementById('scanned-code').innerText = qrCodeMessage;
      qrScanner.stop(); // stop after success
    },
    errorMessage => {
      // can ignore
    }
  );

  // Get GPS & Show Map
  let map, marker;
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
      const lat = position.coords.latitude;
      const lng = position.coords.longitude;

      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;
      document.getElementById('lokasi-coord').innerText = `${lat.toFixed(5)}, ${lng.toFixed(5)}`;

      map = L.map('map').setView([lat, lng], 17);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
      }).addTo(map);
      marker = L.marker([lat, lng]).addTo(map).bindPopup("Lokasi Anda").openPopup();

    }, function() {
      alert("Gagal mengambil lokasi. Aktifkan izin GPS.");
    });
  } else {
    alert("Browser tidak mendukung GPS.");
  }
</script>
@endpush
