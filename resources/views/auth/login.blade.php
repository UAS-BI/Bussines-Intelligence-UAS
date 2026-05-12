<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Paris Housing BI</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    @vite(['resources/css/login.css'])
</head>
<body>
    <main class="login-page"
    style="background-image: url('{{ asset('images/paris_bg.png') }}');">
        <div class="overlay"></div>
        <div class="login-box">
            <div class="login-header">
                <div class="brand-icon">
                    <i class="bi bi-buildings"></i>
                </div>
                <h1>Paris Housing BI</h1>
                <p>Property Analytics Dashboard</p>
            </div>
            @if(session('error'))
                <div class="login-error">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="●●●●●●">
                </div>
                <button type="submit" class="login-btn">
                    Sign In
                </button>
            </form>
        </div>
    </main>
</body>
</html>