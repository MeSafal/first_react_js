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
        
        {{-- Global Header --}}
        <header class="viso-header">
            <div class="dropdown">
                <div class="viso-header-profile dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="text-end d-none d-sm-block">
                        <div class="fw-bold fs-13 text-dark leading-tight">{{ auth()->user()->name }}</div>
                        <div class="text-muted fs-11">{{ auth()->user()->hasRole('Super Admin') ? 'Administrator' : 'Team Member' }}</div>
                    </div>
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&size=30&background=3b82f6&color=fff' }}" 
                         alt="User" class="rounded-circle border border-2 border-white shadow-sm" width="30" height="30">
                </div>
                <ul class="dropdown-menu dropdown-menu-end viso-profile-dropdown border-0" aria-labelledby="profileDropdown">
                    <li class="px-3 py-2 mb-1">
                        <div class="fw-bold fs-14 text-dark">{{ auth()->user()->name }}</div>
                        <div class="text-muted fs-11 text-truncate">{{ auth()->user()->email }}</div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="icon-user"></i> My Profile</a></li>
                    <li><a class="dropdown-item" href="#"><i class="icon-settings"></i> Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="icon-logout"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </header>

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
        <div class="flex-grow-1 position-relative">
            @yield('content')
        </div>

        {{-- Global Footer --}}
        <footer class="mt-auto py-4 px-4 border-top bg-white bg-opacity-50">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <a href="{{ route('kanban') }}" class="text-decoration-none text-muted small hover-text-primary fw-medium">Kanban Board</a>
                    <span class="text-muted opacity-25">|</span>
                    <a href="{{ route('calendar') }}" class="text-decoration-none text-muted small hover-text-primary fw-medium">Calendar</a>
                    <span class="text-muted opacity-25">|</span>
                    <a href="{{ route('my-work') }}" class="text-decoration-none text-muted small hover-text-primary fw-medium">My Work</a>
                </div>
                <div class="text-center text-md-end">
                    <div class="text-muted small fw-medium mb-1">
                        &copy; {{ date('Y') }} <span class="text-primary fw-bold">Visobotic</span>
                    </div>
                    <div class="text-muted tiny opacity-75">
                        Designed and developed by <span class="text-dark fw-bold">Gokul Subedi</span>
                    </div>
                </div>
            </div>
        </footer>

    </main>

    {{-- Global Modals --}}
    @include('partials.task-slide-over')

    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    
    {{-- Custom App Logic --}}
    <script src="{{ asset('js/viso-app.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>

    {{-- Global Confirmation Modal --}}
    <div class="modal fade" id="visoConfirmationModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-body p-4 text-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-flex mb-3">
                        <i class="icon-trash-2 text-danger" style="font-size: 24px;"></i>
                    </div>
                    <h5 class="fw-bold text-dark mb-2" id="visoConfirmTitle">Are you sure?</h5>
                    <p class="text-muted small mb-4" id="visoConfirmMessage">This action cannot be undone. Are you sure you want to proceed?</p>
                    <div class="d-flex gap-2 justify-content-center">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-pill border" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger fw-bold px-4 rounded-pill" id="visoConfirmBtn">Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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
