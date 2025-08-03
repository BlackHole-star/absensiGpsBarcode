<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title') - Absensi</title>

    <!-- Tabler (Bootstrap-based) CSS -->
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet" />

    @stack('styles')
  </head>
  <body class="bg-white d-flex align-items-center" style="min-height: 100vh;">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
          <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="img-fluid" style="max-height: 60px;" />
          </div>

          {{-- Main Auth Content --}}
          @yield('content')
          
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    @stack('scripts')
  </body>
</html>
