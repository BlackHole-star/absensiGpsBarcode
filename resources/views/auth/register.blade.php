@extends('layouts.auth')

@section('title', 'Daftar')

@section('content')
  <form class="card shadow-sm border-0" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="card-body p-5 space-y-4">

      <h2 class="card-title text-center text-xl fw-semibold">Buat Akun Baru</h2>

      @if ($errors->any())
        <div class="alert alert-danger text-sm py-2 px-3 rounded shadow-sm">
          <ul class="mb-0 ps-4">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="form-group">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input 
          type="text" 
          id="name" 
          name="name" 
          class="form-control form-control-lg" 
          placeholder="Nama lengkap Anda"
          value="{{ old('name') }}" 
          required
        >
      </div>

      <div class="form-group">
        <label for="email" class="form-label">Alamat Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control form-control-lg" 
          placeholder="nama@contoh.com"
          value="{{ old('email') }}" 
          required
        >
      </div>

      <div class="form-group">
        <label for="password" class="form-label">Kata Sandi</label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control form-control-lg" 
          placeholder="Buat kata sandi"
          required
        >
      </div>

      <div class="form-group">
        <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
        <input 
          type="password" 
          id="password_confirmation" 
          name="password_confirmation" 
          class="form-control form-control-lg" 
          placeholder="Ulangi kata sandi"
          required
        >
      </div>

      <div class="form-check mb-3">
        <input 
          type="checkbox" 
          class="form-check-input" 
          id="agree" 
          required
        >
        <label class="form-check-label text-sm" for="agree">
          Saya menyetujui <a href="#" class="text-primary text-decoration-none">syarat dan kebijakan</a>
        </label>
      </div>

      <div>
        <button type="submit" class="btn btn-primary btn-lg w-100">
          Daftar
        </button>
      </div>

    </div>
  </form>

  <div class="text-center text-muted mt-4 text-sm">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="text-primary text-decoration-none">Masuk di sini</a>
  </div>
@endsection
