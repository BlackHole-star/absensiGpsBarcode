<!doctype html>
<html lang="id" class="h-100">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', config('app.name', 'Absensi'))</title>

    <!-- Font & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
      body {
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        background-color: #f4f6f8;
        color: #24292f;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
      }

      .navbar {
        background-color: #ffffff;
        border-bottom: 1px solid #d0d7de;
        box-shadow: 0 1px 0 rgba(27,31,36,0.05);
        padding: 0.75rem 1rem;
      }

      .navbar-brand {
        font-weight: 600;
        color: #1f2328;
        font-size: 1rem;
      }

      .nav-link {
        color: #57606a !important;
        font-weight: 500;
      }

      .nav-link:hover,
      .nav-link:focus,
      .nav-link.active {
        color: #1f6feb !important;
        font-weight: 600;
      }

      .main-content {
        flex: 1;
        padding-top: 1.5rem;
        padding-bottom: 1rem;
      }

      .user-info {
        display: flex;
        align-items: center;
        gap: 1rem;
      }

      .btn-logout {
        border: none;
        background-color: #fdd8da;
        color: #c20d22;
        font-weight: 500;
        padding: 0.375rem 0.75rem;
        border-radius: 4px;
        transition: background-color 0.2s ease-in-out;
      }

      .btn-logout:hover {
        background-color: #f9c2c7;
      }

      footer {
        padding: 1rem;
        background: transparent;
        text-align: center;
        font-size: 13px;
        color: #6e7781;
      }

      @media (max-width: 768px) {
        .navbar .nav {
          flex-direction: column;
          align-items: flex-start;
        }

        .navbar .nav-link {
          padding-left: 0;
        }
      }
    </style>

    @stack('styles')
  </head>

  <body>
    {{-- Navigasi --}}
    <nav class="navbar d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <a 
          href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('user.absen.scan') }}" 
          class="navbar-brand">Aplikasi Absensi</a>
      </div>

      <div class="d-none d-md-flex justify-content-center flex-grow-1">
        @auth
          @if(auth()->user()->role === 'admin')
            <ul class="nav">
              <li class="nav-item"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Beranda</a></li>
              <li class="nav-item"><a href="{{ route('admin.barcodes.index') }}" class="nav-link {{ request()->routeIs('admin.barcodes.*') ? 'active' : '' }}">Kode QR</a></li>
              <li class="nav-item"><a href="{{ route('admin.rekap') }}" class="nav-link {{ request()->routeIs('admin.rekap') ? 'active' : '' }}">Rekap Absensi</a></li>
              <li class="nav-item"><a href="{{ route('admin.employees.index') }}" class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">Data Karyawan</a></li>
            </ul>
          @endif
        @endauth
      </div>

      {{-- Info Pengguna + Logout --}}
      @auth
        <div class="dropdown">
          <button class="btn btn-sm dropdown-toggle fw-semibold text-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            Halo, {{ auth()->user()->name }}
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm">
            <li>
              <a class="dropdown-item" href="{{ route('profile.show') }}">Profil</a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="dropdown-item text-danger" type="submit">Keluar</button>
              </form>
            </li>
          </ul>
        </div>
      @endauth
    </nav>

    {{-- Konten Utama --}}
    <main class="container-xl main-content">
      @yield('content')
    </main>

    {{-- Footer --}}
    <footer>
      &copy; {{ date('Y') }} Aplikasi Absensi. Seluruh hak cipta dilindungi.
    </footer>

    {{-- Skrip --}}
    <script src="{{ asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
  </body>
</html>
