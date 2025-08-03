@extends('layouts.app')

@section('title', 'Tambah QR Code')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <h3 class="mb-2">Tambah QR Code Lokasi</h3>
    <a href="{{ route('admin.barcodes.index') }}" class="btn btn-sm btn-outline-secondary mb-4">← Kembali</a>

    @if ($errors->any())
      <div class="alert alert-danger rounded-2">
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.barcodes.store') }}">
      @csrf

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama</label>
          <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Value QR Code</label>
          <div class="input-group">
            <input type="text" name="value" id="barcodeValue" class="form-control" value="{{ old('value') }}" required>
            <button type="button" onclick="generateBarcode()" class="btn btn-outline-dark">Generate</button>
          </div>
        </div>

        <div class="col-md-6">
          <label class="form-label">Latitude</label>
          <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" required readonly>
        </div>

        <div class="col-md-6">
          <label class="form-label">Longitude</label>
          <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" required readonly>
        </div>

        <div class="col-md-6">
          <label class="form-label">Radius Valid Absen (meter)</label>
          <input type="number" name="radius" class="form-control" value="{{ old('radius', 50) }}" required>
        </div>
      </div>

      <div class="my-4">
        <button type="button" onclick="toggleMap()" class="btn btn-outline-secondary">Tampilkan / Sembunyikan Peta</button>
        <div id="map" style="height: 300px; display: none;" class="mt-3 rounded border shadow-sm"></div>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Simpan Barcode</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  let map, marker, circle;

  function toggleMap() {
    const mapDiv = document.getElementById('map');
    mapDiv.style.display = (mapDiv.style.display === 'none') ? 'block' : 'none';
    if (!map) initMap();
  }

  function initMap() {
    const lat = parseFloat(document.getElementById('latitude').value) || -6.2;
    const lng = parseFloat(document.getElementById('longitude').value) || 106.8;

    map = L.map('map').setView([lat, lng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([lat, lng], { draggable: true }).addTo(map);

    marker.on('dragend', function(e) {
      const { lat, lng } = e.target.getLatLng();
      updateCoords(lat, lng);
    });

    map.on('click', function(e) {
      const { lat, lng } = e.latlng;
      updateCoords(lat, lng);
    });

    // Inisialisasi lingkaran kalau sudah ada nilai radius
    const radius = parseFloat(document.querySelector('input[name="radius"]').value);
    if (!isNaN(radius)) {
      drawCircle(lat, lng, radius);
    }
  }

  function updateCoords(lat, lng) {
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);

    if (marker) marker.setLatLng([lat, lng]);

    const radius = parseFloat(document.querySelector('input[name="radius"]').value);
    if (!isNaN(radius)) {
      drawCircle(lat, lng, radius);
    }
  }

  function drawCircle(lat, lng, radius) {
    if (circle) {
      circle.setLatLng([lat, lng]);
      circle.setRadius(radius);
    } else {
      circle = L.circle([lat, lng], {
        color: 'blue',
        fillColor: '#a1c4fd',
        fillOpacity: 0.4,
        radius: radius
      }).addTo(map);
    }
  }

  function getUserLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const lat = pos.coords.latitude;
          const lng = pos.coords.longitude;
          updateCoords(lat, lng);
        },
        (err) => {
          console.warn('Gagal ambil lokasi:', err.message);
        }
      );
    } else {
      alert("Geolocation tidak didukung browser ini.");
    }
  }

  function generateBarcode() {
    const year = new Date().getFullYear();
    const random = Math.floor(1000 + Math.random() * 9000);
    const value = `LOC-${year}-${random}`;
    document.getElementById('barcodeValue').value = value;
  }

  // Update lingkaran kalau radius diubah manual
  document.addEventListener('DOMContentLoaded', function () {
    const radiusInput = document.querySelector('input[name="radius"]');
    radiusInput.addEventListener('input', () => {
      const radius = parseFloat(radiusInput.value);
      const lat = parseFloat(document.getElementById('latitude').value);
      const lng = parseFloat(document.getElementById('longitude').value);

      if (!isNaN(lat) && !isNaN(lng) && !isNaN(radius)) {
        drawCircle(lat, lng, radius);
      }
    });
  });

  window.addEventListener('load', getUserLocation);
</script>
@endpush

