<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Visobotics') }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Lucide Icons --}}
    <link href="https://unpkg.com/lucide-static@latest/font/lucide.css" rel="stylesheet">
    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    {{-- Visobotics Theme --}}
    <link href="{{ asset('css/viso-theme.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body class="bg-light">
    <div class="viso-app" id="visoApp">

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Main Content --}}
        <main class="viso-main viso-scroll" id="visoMain">
            @yield('content')
        </main>

        {{-- Task Slide-Over Modal --}}
        @include('partials.task-modal')

    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- Visobotics App JS --}}
    <script src="{{ asset('js/viso-app.js') }}"></script>

    @stack('scripts')
</body>
</html>
