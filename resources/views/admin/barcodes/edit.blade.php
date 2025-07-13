@extends('layouts.app')

@section('content')
<h2>Edit Barcode</h2>

@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0">
      @foreach ($errors->all() as $e)
        <li>{{ $e }}</li>
      @endforeach
    </ul>
  </div>
@endif

<form method="POST" action="{{ route('admin.barcodes.update', $barcode->id) }}">
    @method('PUT')

  @csrf
  @method('PUT')

  <div class="mb-3">
    <label>Nama</label>
    <input type="text" name="name" class="form-control" value="{{ $barcode->name }}" required>
  </div>

  <div class="mb-3">
    <label>Value</label>
    <input type="text" name="value" class="form-control" value="{{ $barcode->value }}" required>
  </div>

  <div class="mb-3">
    <label>Latitude</label>
    <input type="text" name="latitude" class="form-control" value="{{ $barcode->latitude }}" required>
  </div>

  <div class="mb-3">
    <label>Longitude</label>
    <input type="text" name="longitude" class="form-control" value="{{ $barcode->longitude }}" required>
  </div>

  <div class="mb-3">
    <label>Radius (meter)</label>
    <input type="number" name="radius" class="form-control" value="{{ $barcode->radius }}" required>
  </div>

  <button type="submit" class="btn btn-success">Update</button>
</form>
@endsection
