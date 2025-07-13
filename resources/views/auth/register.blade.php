@extends('layouts.auth')

@section('title', 'Register')

@section('content')
  <form class="card shadow-sm" action="{{ route('register') }}" method="POST">
    @csrf
    <div class="card-body p-6 space-y-4">
      <h2 class="card-title text-center text-lg font-semibold">Create new account</h2>

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
        <label class="form-label">Name</label>
        <input type="text" name="name" class="form-control focus:ring focus:ring-blue-200" value="{{ old('name') }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control focus:ring focus:ring-blue-200" value="{{ old('email') }}" required>
      </div>

      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control focus:ring focus:ring-blue-200" required>
      </div>

      <div class="form-group">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="password_confirmation" class="form-control focus:ring focus:ring-blue-200" required>
      </div>

      <div class="form-group">
        <label class="custom-control custom-checkbox items-center">
          <input type="checkbox" class="custom-control-input" required>
          <span class="custom-control-label text-sm">
            I agree to the <a href="#" class="text-blue-500 hover:underline">terms and policy</a>
          </span>
        </label>
      </div>

      <div class="form-footer">
        <button type="submit" class="btn btn-primary w-full">Create account</button>
      </div>
    </div>
  </form>

  <div class="text-center text-muted mt-3 text-sm">
    Already have an account?
    <a href="{{ route('login') }}" class="text-blue-500 hover:underline">Sign in</a>
  </div>
@endsection
