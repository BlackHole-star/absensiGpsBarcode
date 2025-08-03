@extends('layouts.app')

@section('title', 'Edit Karyawan')

@section('content')
<div class="container my-4 px-3 px-md-5">
  <div class="bg-white p-4 p-md-5 rounded-3 shadow-sm">

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
      <h3 class="mb-0">Edit Data Karyawan</h3>
      <a href="{{ route('admin.employees.index') }}" class="btn btn-outline-secondary">
        ← Kembali ke Daftar
      </a>
    </div>

    @if ($errors->any())
      <div class="alert alert-danger rounded-2">
        <strong class="d-block mb-2">Terdapat beberapa kesalahan:</strong>
        <ul class="mb-0 ps-3">
          @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('admin.employees.update', $employee->id) }}" class="needs-validation" novalidate>
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $employee->name) }}">
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-4">
        <label for="email" class="form-label">Alamat Email</label>
        <input type="email" name="email" id="email" class="form-control" required value="{{ old('email', $employee->email) }}">
        <div class="invalid-feedback">Email tidak boleh kosong dan harus valid.</div>
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-success px-4">Simpan Perubahan</button>
      </div>
    </form>

  </div>
</div>
@endsection

@push('scripts')
<script>
  // Bootstrap validation
  (() => {
    'use strict';
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();
</script>
@endpush
