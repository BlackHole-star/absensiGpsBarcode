@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
  <form class="card shadow-sm border-0" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="card-body p-5 space-y-4">

      <h2 class="card-title text-center text-xl fw-semibold">Masuk ke Akun Anda</h2>

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
        <label for="email" class="form-label">Alamat Email</label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          class="form-control form-control-lg" 
          placeholder="nama@contoh.com"
          value="{{ old('email') }}" 
          required 
          autofocus
        >
      </div>

      <div class="form-group">
        <label for="password" class="form-label d-flex justify-between align-items-center">
          <span>Kata Sandi</span>
          <a href="#" class="text-sm text-primary text-decoration-none">Lupa kata sandi?</a>
        </label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          class="form-control form-control-lg" 
          placeholder="Masukkan kata sandi" 
          required
        >
      </div>

      <div class="form-check mb-3">
        <input 
          class="form-check-input" 
          type="checkbox" 
          name="remember" 
          id="remember"
        >
        <label class="form-check-label" for="remember">
          Ingat saya
        </label>
      </div>

      <div>
        <button type="submit" class="btn btn-primary btn-lg w-100">
          Masuk
        </button>
      </div>

    </div>
  </form>

  <div class="text-center text-muted mt-4 text-sm">
    Belum punya akun?
    <a href="{{ route('register') }}" class="text-primary text-decoration-none">Daftar sekarang</a>
  </div>
@endsection
