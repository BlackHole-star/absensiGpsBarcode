<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Absensi'))</title>

    <!-- Tabler CSS -->
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    @stack('styles')
  </head>
  <body class="bg-white position-relative">

    <div class="page position-relative">
      {{-- Admin Navbar --}}
      @auth
        @if(auth()->user()->role === 'admin')
          <header class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
              <a class="navbar-brand" href="#">Absensi</a>
              <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
              </button>
              <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Dashboard</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.barcodes.index') }}">Data Barcode</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.rekap') }}">Rekap Absensi</a>
                  </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                  <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                      @csrf
                      <button class="btn btn-outline-danger btn-sm">Logout</button>
                    </form>
                  </li>
                </ul>
              </div>
            </div>
          </header>
        @endif
      @endauth

      {{-- User Logout Button --}}
      @auth
        @if(auth()->user()->role === 'user')
          <div class="position-fixed top-0 end-0 m-3 z-index-sticky d-block d-lg-none">
            <form action="{{ route('logout') }}" method="POST">
              @csrf
              <button class="btn btn-sm btn-outline-danger">Logout</button>
            </form>
          </div>
        @endif
      @endauth

      {{-- Main Content --}}
      <div class="page-wrapper layout-fluid">
        <div class="page-body">
          <div class="container-xl mt-3">
            @yield('content')
          </div>
        </div>

        {{-- Footer --}}
        <footer class="footer footer-transparent d-print-none mt-4">
          <div class="container-xl text-center">
            &copy; {{ date('Y') }} - Aplikasi Absensi
          </div>
        </footer>
      </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    @stack('scripts')
  </body>
</html>
