@extends('layouts.app')

@section('title', 'Tambah Barcode')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@section('content')
<div class="container-xl my-4 px-3 px-md-5">
  <h2 class="mb-4">Tambah Barcode Lokasi</h2>

  @if ($errors->any())
    <div class="alert alert-danger">
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
        <label class="form-label">Value Barcode</label>
        <input type="text" name="value" class="form-control" value="{{ old('value') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Latitude</label>
        <input type="text" name="latitude" id="latitude" class="form-control" value="{{ old('latitude') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Longitude</label>
        <input type="text" name="longitude" id="longitude" class="form-control" value="{{ old('longitude') }}" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Radius Valid Absen (meter)</label>
        <input type="number" name="radius" class="form-control" value="{{ old('radius', 50) }}" required>
      </div>
    </div>

    <div class="my-4">
      <button type="button" onclick="toggleMap()" class="btn btn-secondary">Tampilkan/Sembunyikan Peta</button>
      <div id="map" style="height: 300px; display: none;" class="mt-3 rounded shadow-sm border"></div>
    </div>

    <div class="text-end">
      <button type="submit" class="btn btn-primary">Simpan Barcode</button>
    </div>
  </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  function toggleMap() {
    const mapDiv = document.getElementById('map');
    mapDiv.style.display = (mapDiv.style.display === 'none') ? 'block' : 'none';
  }

  window.addEventListener('load', function () {
    const defaultLat = -6.2;
    const defaultLng = 106.8;

    const map = L.map('map').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    let marker;

    map.on('click', function (e) {
      const lat = e.latlng.lat.toFixed(6);
      const lng = e.latlng.lng.toFixed(6);

      document.getElementById('latitude').value = lat;
      document.getElementById('longitude').value = lng;

      if (marker) {
        marker.setLatLng([lat, lng]);
      } else {
        marker = L.marker([lat, lng]).addTo(map);
      }
    });
  });
</script>
@endpush
