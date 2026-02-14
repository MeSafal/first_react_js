<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Visobotics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/viso-theme.css') }}" rel="stylesheet">
    <style>
        body { background-color: #f8fafc; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .auth-card { width: 100%; max-width: 440px; padding: 2.5rem; background: white; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="text-center mb-4">
            <div class="bg-primary rounded d-inline-flex align-items-center justify-content-center mb-2" style="width:48px;height:48px">
                <span class="fw-bold text-white h4 mb-0">V</span>
            </div>
            <h1 class="h4 fw-bold text-dark">Join Visobotics</h1>
            <p class="text-muted small">Create your account and start managing tasks</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger small py-2">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Full Name</label>
                <input type="text" name="name" class="form-control" required autofocus value="{{ old('name') }}">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Email Address</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Password</label>
                <input type="password" name="password" class="form-control" required autocomplete="new-password">
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Create Account</button>
        </form>

        <div class="mt-4 text-center">
            <p class="small text-muted mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-none fw-bold">Login</a></p>
        </div>
    </div>
</body>
</html>
