<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/nuclark-auth.css') }}?v={{ file_exists(public_path('css/nuclark-auth.css')) ? filemtime(public_path('css/nuclark-auth.css')) : '1' }}">
</head>
<body>
<div class="auth-shell">
    <aside class="auth-rail">
        <div class="auth-brand">
            <div class="auth-mark"><img src="{{ asset('images/nu-logo.png') }}" alt="National University"></div>
            <div>
                <div class="auth-brand-name">NU Clark</div>
                <div class="auth-brand-sub">National University</div>
            </div>
        </div>
        <h1 class="auth-statement">Assets, inventory and facilities in one place.</h1>
        <p class="auth-lede">CAPEX monitoring, OPEX requisitions, forecasting and venue reservations for the NU Clark campus.</p>
        <div class="auth-poster">
            <div class="auth-poster-text">Education that works.</div>
            <div class="auth-poster-list"><span>Asset Office</span><span>Facilities Office</span><span>Dept. User</span></div>
        </div>
    </aside>

    <main class="auth-main">
        <div class="auth-form-wrap">
            <div class="auth-eyebrow">NU Clark Asset Management</div>
            <h2 class="auth-title">Log in</h2>
            <p class="auth-sub">Integrated asset, inventory and facilities platform. Use the email tied to your account.</p>

            <form method="POST" action="{{ route('login.submit') }}" class="auth-form">
                @csrf

                <div class="auth-field">
                    <label class="form-label" for="loginEmail">Email address</label>
                    <input type="email" name="email" id="loginEmail" class="form-control" value="{{ old('email') }}" placeholder="you@nuclark.local" required autofocus>
                </div>
                <div class="auth-field">
                    <label class="form-label" for="loginPassword">Password</label>
                    <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Enter your password" required>
                    <div class="auth-help"><a href="{{ route('password.request') }}">Forgot password?</a></div>
                </div>
                <button class="btn-login">Log in</button>
            </form>

            <div class="auth-alt">
                <span>New to the system?</span>
                <a href="{{ route('register') }}" class="btn-main btn-ghost">Create an account</a>
            </div>
        </div>
    </main>
</div>
@include('auth.partials.password-toggle')
@include('partials.alerts')
</body>
</html>
