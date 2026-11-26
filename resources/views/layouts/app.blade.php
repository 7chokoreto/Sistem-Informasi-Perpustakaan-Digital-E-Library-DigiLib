<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'DigiLib') - Perpustakaan Umum</title>

  <!-- Favicon -->
  <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
  <link rel="alternate icon" type="image/png" href="{{ asset('favicon.png') }}">

  <!-- Google Fonts & Font Awesome CDN -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Core & Neumorphic Theme Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/superadmin.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/pustakawan.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/member.css') }}">

  @stack('styles')
</head>
<body class="dashboard-body">

  <div class="app-container">

    <!-- Dynamic Role-Based Sidebar -->
    @auth
      @if(auth()->user()->isSuperadmin())
        @include('components.sidebar-superadmin')
      @elseif(auth()->user()->isPustakawan())
        @include('components.sidebar-pustakawan')
      @else
        @include('components.sidebar-member')
      @endif
    @endauth

    <!-- Main Content Wrapper -->
    <main class="main-wrapper">
      @yield('content')
    </main>

  </div>

  <!-- Logout Modal -->
  @include('components.modal-logout')

  <!-- Top-Left Toast Bubble Notification System with Countdown Progress Bar -->
  @include('components.toast-bubble')

  <!-- Main Global Scripts -->
  <script src="{{ asset('assets/js/main.js') }}"></script>
  @stack('scripts')
</body>
</html>
