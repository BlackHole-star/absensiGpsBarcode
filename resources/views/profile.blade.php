@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="container">
  <div class="bg-white p-4 rounded shadow-sm">
    <h4 class="mb-4">Profil {{ ucfirst($user->role) }}</h4>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label">Nama</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
      </div>

      <hr>

      <h5 class="mt-4">Ubah Password (Opsional)</h5>

      <div class="mb-3">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-control" placeholder="Biarkan kosong jika tidak ingin mengganti">
      </div>

      <div class="mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control">
      </div>

      <div class="text-end">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>
@endsection
