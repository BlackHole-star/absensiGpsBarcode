@extends('layouts.app')

@section('title', 'Scan Absen')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
  #qr-reader video,
  #qr-reader canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover;
    border-radius: 0.5rem;
  }
  .mirror-video {
    transform: scaleX(-1);
  }
  .absen-status-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    flex: 1;
    min-width: 0;
  }
  .btn-sm-custom {
    padding: 6px 12px;
    font-size: 0.875rem;
  }
</style>
@endpush

@section('content')
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="fw-semibold mb-0">Scan QR & Lokasi</h4>
    @auth
    {{-- <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn btn-sm btn-outline-danger">Logout</button>
    </form> --}}
    @endauth
  </div>

  <div class="bg-white rounded-3 shadow-sm p-4">
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold small mb-1">Waktu Kerja</label>
        <input type="text" class="form-control form-control-sm" value="09:00 - 17:00" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold small mb-1">Tanggal</label>
        <input type="text" class="form-control form-control-sm" value="{{ now()->format('d/m/Y') }}" readonly>
        <small class="text-muted mt-1 d-block">Lokasi Anda: <span id="currentLocation">Mendeteksi...</span></small>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold small mb-1">QR Scanner</label>
        <div class="border rounded bg-dark" style="width: 100%; aspect-ratio: 1 / 1;">
          <div id="qr-reader" style="width: 100%; height: 100%;"></div>
        </div>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold small mb-1">Peta Lokasi</label>
        <div id="map" class="border rounded" style="width: 100%; height: 240px;"></div>

        <div class="d-flex gap-2 mt-3 flex-column flex-sm-row">
          <div class="absen-status-box">
            <div class="text-muted small mb-1">Absen Masuk</div>
            <div class="fw-semibold">{{ $attendance?->time_in ?? '-' }}</div>
          </div>
          <div class="absen-status-box">
            <div class="text-muted small mb-1">Absen Keluar</div>
            <div class="fw-semibold">{{ $attendance?->time_out ?? '-' }}</div>
          </div>
          <div class="absen-status-box">
            <div class="text-muted small mb-1">Koordinat</div>
            <div class="fw-semibold">{{ $attendance?->latitude ? $attendance->latitude . ', ' . $attendance->longitude : '-' }}</div>
          </div>
        </div>

        <div class="d-flex gap-2 mt-3 flex-column flex-sm-row">
          <a href="{{ route('user.absen.izin') }}" class="btn btn-warning w-100 btn-sm-custom">Ajukan Izin</a>
          <a href="{{ route('user.absen.history') }}" class="btn btn-primary w-100 btn-sm-custom">Riwayat Absen</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  let lat = null, lon = null;

  function initMapAndQR() {
    navigator.geolocation.getCurrentPosition(function(position) {
      lat = position.coords.latitude;
      lon = position.coords.longitude;

      document.getElementById('currentLocation').textContent = `${lat.toFixed(5)}, ${lon.toFixed(5)}`;

      const map = L.map('map').setView([lat, lon], 16);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
      L.marker([lat, lon]).addTo(map);
      setTimeout(() => map.invalidateSize(), 300);
    }, function(err) {
      alert("Gagal mengakses lokasi: " + err.message);
    });

    Html5Qrcode.getCameras().then(devices => {
      if (devices.length) {
        const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        let selectedDevice = devices[0];

        if (isMobile) {
          const backCam = devices.find(d => /back|rear|environment/i.test(d.label));
          if (backCam) selectedDevice = backCam;
        }

        const qrScanner = new Html5Qrcode("qr-reader");
        const disableFlip = isMobile;

        qrScanner.start(
          { deviceId: { exact: selectedDevice.id } },
          {
            fps: 10,
            qrbox: function(w, h) {
              const min = Math.min(w, h);
              return { width: min * 0.6, height: min * 0.6 };
            },
            disableFlip: disableFlip
          },
          function(decodedText) {
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
              .catch(err => alert("Gagal mengirim absen: " + err));
            });
          },
          function(error) {
            // silent
          }
        );

        if (!isMobile) {
          const videoCheck = setInterval(() => {
            const videoEl = document.querySelector('#qr-reader video');
            if (videoEl) {
              videoEl.classList.add('mirror-video');
              clearInterval(videoCheck);
            }
          }, 200);
        }
      } else {
        alert("Kamera tidak ditemukan.");
      }
    }).catch(err => {
      alert("Tidak bisa mengakses kamera: " + err);
    });
  }

  window.addEventListener('DOMContentLoaded', initMapAndQR);
</script>
@endpush
