<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Absensi</title>

    <!-- Tabler CSS -->
    <link href="{{ asset('assets/css/dashboard.css') }}" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @stack('styles')
  </head>
  <body class="bg-white">
    <div class="page">
      <div class="page-single">
        <div class="container">
          <div class="row">
            <div class="col col-login mx-auto">
              <div class="text-center mb-4">
                <img src="{{ asset('assets/images/logo.png') }}" class="h-6" alt="Logo">
              </div>

              {{-- Main Auth Content --}}
              @yield('content')

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/vendors/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>

    @stack('scripts')
  </body>
</html>
