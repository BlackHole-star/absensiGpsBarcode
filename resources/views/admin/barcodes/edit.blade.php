@extends('layouts.app')

@section('title', 'Edit QR Code')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <h3 class="mb-4">Edit QR Code Lokasi</h3>

    <a href="{{ route('admin.barcodes.index') }}" class="btn btn-outline-secondary mb-3">
      ← Kembali
    </a>

    @if ($errors->any())
      <div class="alert alert-danger rounded-2">
        <ul class="mb-0">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.barcodes.update', $barcode->id) }}">
      @csrf
      @method('PUT')

      <div class="row g-3 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Nama</label>
          <input type="text" name="name" class="form-control" value="{{ $barcode->name }}" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Value QR Code</label>
          <input type="text" name="value" class="form-control" value="{{ $barcode->value }}" required>
        </div>
      </div>

      <div class="row g-3 mt-1">
        <div class="col-md-6">
          <label class="form-label">Latitude</label>
          <input type="text" name="latitude" id="latitude" class="form-control" value="{{ $barcode->latitude }}" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Longitude</label>
          <input type="text" name="longitude" id="longitude" class="form-control" value="{{ $barcode->longitude }}" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Radius Valid Absen (meter)</label>
          <input type="number" name="radius" class="form-control" value="{{ $barcode->radius }}" required>
        </div>
      </div>

      <div class="my-4">
        <button type="button" onclick="toggleMap()" class="btn btn-outline-secondary">
          Tampilkan / Sembunyikan Peta
        </button>
        <div id="map" style="height: 300px; display: none;" class="mt-3 rounded border shadow-sm"></div>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-success">Update Barcode</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  let map;
  let marker;
  let circle;

  function toggleMap() {
    const mapDiv = document.getElementById('map');
    const isHidden = mapDiv.style.display === 'none';

    mapDiv.style.display = isHidden ? 'block' : 'none';

    if (isHidden) {
      setTimeout(() => map.invalidateSize(), 200);
    }
  }

  function drawCircle(lat, lng, radius) {
    if (circle) {
      map.removeLayer(circle);
    }
    circle = L.circle([lat, lng], {
      radius: radius,
      color: '#0d6efd',
      fillColor: '#0d6efd',
      fillOpacity: 0.2,
    }).addTo(map);
  }

  function updatePosition(lat, lng) {
    marker.setLatLng([lat, lng]);
    document.getElementById('latitude').value = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
    const radius = parseFloat(document.querySelector('input[name="radius"]').value) || 50;
    drawCircle(lat, lng, radius);
  }

  window.addEventListener('load', function () {
    const initialLat = parseFloat("{{ $barcode->latitude }}") || -6.2;
    const initialLng = parseFloat("{{ $barcode->longitude }}") || 106.8;
    const initialRadius = parseFloat("{{ $barcode->radius }}") || 50;

    map = L.map('map').setView([initialLat, initialLng], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
    drawCircle(initialLat, initialLng, initialRadius);

    marker.on('dragend', function (e) {
      const { lat, lng } = e.target.getLatLng();
      updatePosition(lat, lng);
    });

    map.on('click', function (e) {
      const { lat, lng } = e.latlng;
      updatePosition(lat, lng);
    });

    document.querySelector('input[name="radius"]').addEventListener('input', function () {
      const radius = parseFloat(this.value) || 0;
      const lat = parseFloat(document.getElementById('latitude').value);
      const lng = parseFloat(document.getElementById('longitude').value);
      drawCircle(lat, lng, radius);
    });
  });
</script>

@endpush
