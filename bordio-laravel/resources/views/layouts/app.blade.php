<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Visobotics') - Premium Admin Panel</title>

    {{-- Favicons --}}
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/logo/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/logo/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('img/logo/favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/logo/favicon.ico') }}">

    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Icons (Feather Icons via CDN for simplicity, or use local if available) --}}
    <link href="https://unpkg.com/feather-icons" rel="stylesheet">
    {{-- Simple Line Icons as fallback/addition --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/simple-line-icons/2.5.5/css/simple-line-icons.min.css">

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom Premium Theme --}}
    <link href="{{ asset('css/viso-theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="viso-app">

    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content Wrapper --}}
    <main class="viso-main d-flex flex-column h-100 position-relative">
        
        {{-- Mobile Header (Visible only on small screens) --}}
        <div class="d-md-none p-3 border-bottom bg-white d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="viso-logo-icon" style="width:32px;height:32px;font-size:14px">B</div>
                <span class="fw-bold text-dark">Bordio</span>
            </div>
            <button class="btn btn-light btn-sm" onclick="document.querySelector('.viso-sidebar').classList.toggle('mobile-open')">
                <i class="icon-menu"></i>
            </button>
        </div>

        {{-- Content Area --}}
        <div class="flex-grow-1 position-relative overflow-hidden">
            @yield('content')
        </div>

    </main>

    {{-- Global Modals --}}
    @include('partials.task-modal')

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    
    {{-- Custom App Logic --}}
    <script src="{{ asset('js/viso-app.js') }}"></script>

    <script>
        // Initialize Feather Icons
        feather.replace();
        
        // Mobile Sidebar Close on Click Outside
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.viso-sidebar');
            const toggleBtn = document.querySelector('.d-md-none .btn');
            if (window.innerWidth < 768 && 
                sidebar.classList.contains('mobile-open') && 
                !sidebar.contains(e.target) && 
                !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
