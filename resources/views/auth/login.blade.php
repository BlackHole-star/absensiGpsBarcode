@extends('layouts.auth')

@section('title', 'Login')

@section('content')
  <form class="card shadow-sm" action="{{ route('login') }}" method="POST">
    @csrf
    <div class="card-body p-6 space-y-4">

      <h2 class="card-title text-center text-lg font-semibold">Login to your account</h2>

      @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative text-sm">
          <ul class="mb-0 list-disc ps-5">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="form-group">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control focus:ring focus:ring-blue-200" value="{{ old('email') }}" required autofocus>
      </div>

      <div class="form-group">
        <label class="form-label d-flex justify-between items-center">
          <span>Password</span>
          <a href="#" class="text-xs text-blue-500 hover:underline">Forgot password?</a>
        </label>
        <input type="password" name="password" class="form-control focus:ring focus:ring-blue-200" required>
      </div>

      <div class="form-group">
        <label class="custom-control custom-checkbox items-center">
          <input type="checkbox" class="custom-control-input" name="remember">
          <span class="custom-control-label">Remember me</span>
        </label>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary w-full">Sign in</button>
      </div>
    </div>
  </form>

  <div class="text-center text-muted mt-3 text-sm">
    Don't have an account?
    <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Sign up</a>
  </div>
@endsection
