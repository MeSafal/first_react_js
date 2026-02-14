<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Visobotics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/viso-theme.css') }}" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 400px; padding: 2rem; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="bg-primary rounded d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px">
                <span class="fw-bold text-white h4 mb-0">V</span>
            </div>
            <h1 class="h4 fw-bold text-dark">Welcome Back</h1>
            <p class="text-muted small">Login to your Visobotics account</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Email Address</label>
                <input type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <div class="d-flex justify-content-between">
                    <label class="form-label small fw-bold text-muted">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot?</a>
                    @endif
                </div>
                <input type="password" name="password" class="form-control" required autocomplete="current-password">
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label small text-muted" for="remember">Remember me</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Login</button>
        </form>

        <div class="mt-4 text-center">
            <p class="small text-muted mb-0">Don't have an account? <a href="{{ route('register') }}" class="text-decoration-none fw-bold">Sign up</a></p>
        </div>
    </div>
</body>
</html>
