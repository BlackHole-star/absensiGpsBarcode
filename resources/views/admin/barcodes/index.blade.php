@extends('layouts.app')

@section('title', 'Data QR Code')

@section('content')
<div class="container my-4 px-3 px-md-5">
  {{-- Wrapper putih --}}
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
      <h3 class="mb-0">Daftar Kode QR</h3>
      <a href="{{ route('admin.barcodes.create') }}" class="btn btn-sm btn-primary d-flex align-items-center gap-1">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
          <path d="M8 1a7 7 0 1 1 0 14A7 7 0 0 1 8 1zm0 1A6 6 0 1 0 8 13a6 6 0 0 0 0-12z"/>
          <path d="M8 4a.5.5 0 0 1 .5.5v2.5H11a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V8.5H5a.5.5 0 0 1 0-1h2.5V4.5A.5.5 0 0 1 8 4z"/>
        </svg>
        Tambah Kode QR
      </a>
    </div>

    <div class="row g-4">
      @forelse($barcodes as $barcode)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm border-0 rounded-3">
            <div class="card-body d-flex flex-column justify-content-between">

              {{-- QR Code --}}
              <div class="text-center mb-3" id="qrcode-{{ $barcode->id }}"></div>

              {{-- Nama Lokasi --}}
              <h5 class="fw-semibold text-center">{{ $barcode->name }}</h5>

              {{-- Informasi --}}
              <ul class="list-unstyled small text-muted mb-3">
                <li><strong>Isi Kode:</strong> {{ $barcode->value }}</li>
                <li>
                  <strong>Koordinat:</strong>
                  <a href="https://www.google.com/maps/search/?api=1&query={{ $barcode->latitude }},{{ $barcode->longitude }}" target="_blank">
                    {{ $barcode->latitude }}, {{ $barcode->longitude }}
                  </a>
                </li>
                <li><strong>Radius:</strong> {{ $barcode->radius }} meter</li>
              </ul>

              {{-- Aksi --}}
              <div class="d-flex justify-content-end gap-2 mt-auto">
                <a href="{{ route('admin.barcodes.edit', $barcode->id) }}" class="btn btn-sm btn-outline-warning" title="Ubah QR Code">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
                    <path d="M12.854.146a.5.5 0 0 0-.708 0L10.5 1.793 14.207 5.5l1.646-1.646a.5.5 0 0 0 0-.708L12.854.146zm.146 2.707L11.5 1.5 3 10v2h2l8.5-8.5z"/>
                    <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15H13a.5.5 0 0 0 0-1H2.5a.5.5 0 0 1-.5-.5V3a.5.5 0 0 0-1 0v10.5z"/>
                  </svg>
                </a>

                <a href="{{ route('admin.barcodes.download', $barcode->id) }}" class="btn btn-sm btn-outline-success" title="Unduh QR">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                    <path d="M.5 9.9a.5.5 0 0 1 .5.5V13a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-2.6a.5.5 0 0 1 1 0V13a3 3 0 0 1-3 3H3a3 3 0 0 1-3-3v-2.6a.5.5 0 0 1 .5-.5z"/>
                    <path d="M7.646 10.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 1 0-.708-.708L8.5 9.293V1.5a.5.5 0 0 0-1 0v7.793L5.354 7.146a.5.5 0 1 0-.708.708l3 3z"/>
                  </svg>
                </a>

                <form action="{{ route('admin.barcodes.destroy', $barcode->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus QR Code ini?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5V6h1v-.5a1 1 0 0 0-1-1H10V4a2 2 0 1 0-4 0v.5H4a1 1 0 0 0-1 1V6h1v-.5zm1-1V4a1 1 0 1 1 2 0v.5H6.5z"/>
                      <path d="M14.5 6a.5.5 0 0 1-.5.5h-12a.5.5 0 0 1 0-1h12a.5.5 0 0 1 .5.5zm-11 1v7.5a1 1 0 0 0 1 1H11a1 1 0 0 0 1-1V7h1v7.5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7h1z"/>
                    </svg>
                  </button>
                </form>
              </div>

            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="alert alert-warning text-center mb-0">Belum ada kode QR yang terdaftar.</div>
        </div>
      @endforelse
    </div>
  </div>
</div>

{{-- QR Generator --}}
<script src="{{ asset('assets/js/qrcode.min.js') }}"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    @foreach($barcodes as $barcode)
      new QRCode(document.getElementById("qrcode-{{ $barcode->id }}"), {
        text: @json($barcode->value),
        width: 128,
        height: 128,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
      });
    @endforeach
  });
</script>
@endsection
