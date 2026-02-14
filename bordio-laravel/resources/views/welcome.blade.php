<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Bordio - Premium Admin Panel</title>
        {{-- Custom Premium Theme --}}
        <link href="{{ asset('css/viso-theme.css') }}" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <style>
            body { 
                font-family: 'Inter', sans-serif; 
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                color: #fff;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0;
            }
            .welcome-card {
                background: rgba(255, 255, 255, 0.05);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 3rem;
                text-align: center;
                box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
                max-width: 500px;
                width: 100%;
            }
            .btn-start {
                background: linear-gradient(135deg, #3b82f6, #6366f1);
                color: white;
                padding: 0.8rem 2rem;
                border-radius: 99px;
                text-decoration: none;
                font-weight: 600;
                display: inline-block;
                margin-top: 1.5rem;
                transition: transform 0.2s;
            }
            .btn-start:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="welcome-card">
            <div style="font-size: 64px; margin-bottom: 1rem;">🚀</div>
            <h1 style="font-size: 2.5rem; font-weight: 800; margin-bottom: 0.5rem;">Bordio</h1>
            <p style="color: #94a3b8; font-size: 1.1rem; line-height: 1.6;">
                Premium project management specifically designed for high-performance teams.
            </p>
            
            @if (Route::has('login'))
                <div class="mt-4">
                    @auth
                        <a href="{{ url('/my-work') }}" class="btn-start">Go to My Work</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-start">Login</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" style="color: #94a3b8; text-decoration: none; margin-left: 1rem; font-size: 0.9rem;">Register</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </body>
</html>
