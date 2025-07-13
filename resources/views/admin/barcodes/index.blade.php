@extends('layouts.app')

@section('title', 'Data Barcode')

@section('content')
<div class="container-xl my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Daftar Barcode</h2>
    <a href="{{ route('admin.barcodes.create') }}" class="btn btn-primary">
      <i class="fe fe-plus me-1"></i> Tambah Barcode
    </a>
  </div>

  <div class="row g-4">
    @forelse($barcodes as $barcode)
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            {{-- QR Code --}}
            <div id="qrcode-{{ $barcode->id }}" class="mb-3"></div>

            {{-- Nama Barcode --}}
            <h4 class="fw-bold">{{ $barcode->name }}</h4>

            {{-- Detail --}}
            <ul class="list-unstyled text-muted small mb-3">
              <li><strong>Value:</strong> {{ $barcode->value }}</li>
              <li>
                <strong>Koordinat:</strong>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $barcode->latitude }},{{ $barcode->longitude }}" target="_blank">
                  {{ $barcode->latitude }}, {{ $barcode->longitude }}
                </a>
              </li>
              <li><strong>Radius:</strong> {{ $barcode->radius }} meter</li>
            </ul>

            {{-- Aksi --}}
            <div class="d-flex justify-content-center gap-2">
              <a href="{{ route('admin.barcodes.edit', $barcode->id) }}" class="btn btn-warning btn-sm">
                <i class="fe fe-edit"></i> Edit
              </a>
              <a href="{{ route('admin.barcodes.download', $barcode->id) }}" class="btn btn-success btn-sm">
                <i class="fe fe-download"></i> Download
              </a>
              <form action="{{ route('admin.barcodes.destroy', $barcode->id) }}" method="POST" onsubmit="return confirm('Yakin ingin hapus barcode ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                  <i class="fe fe-trash"></i> Hapus
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-warning text-center">Belum ada barcode terdaftar</div>
      </div>
    @endforelse
  </div>
</div>

{{-- QR Code --}}
<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    @foreach($barcodes as $barcode)
      new QRCode(document.getElementById("qrcode-{{ $barcode->id }}"), {
        text: @json($barcode->value),
        width: 150,
        height: 150,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    @endforeach
  });
</script>
@endsection
