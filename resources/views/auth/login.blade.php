<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--navy-950:#080D22;--navy-900:#0C1330;--navy-800:#141B42;--navy-700:#1D2657;--gold-600:#C9932E;--gold-500:#E3B04E;--gold-400:#F0C876;--ink-500:#666E88}
        *{box-sizing:border-box}
        body{min-height:100vh;margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:
            radial-gradient(circle at 15% 8%, rgba(227,176,78,.10), transparent 32%),
            radial-gradient(circle at 88% 92%, rgba(227,176,78,.08), transparent 38%),
            linear-gradient(155deg,var(--navy-800) 0%,var(--navy-900) 45%,var(--navy-950) 100%);
            display:grid;place-items:center;overflow:hidden}
        .wrap{position:relative;width:100%;min-height:100vh;display:grid;place-items:center;padding:28px}
        .backdrop-word{position:absolute;inset:0;display:grid;place-items:center;font-family:'Lexend',sans-serif;font-weight:800;font-size:82px;line-height:.95;color:rgba(255,255,255,.05);text-align:center;letter-spacing:.01em;pointer-events:none}
        .backdrop-sub{position:absolute;bottom:90px;color:rgba(240,200,118,.5);font-size:15px;font-weight:500;letter-spacing:.14em;text-transform:uppercase;pointer-events:none}
        .login-card{position:relative;z-index:1;width:388px;max-width:92vw;background:rgba(255,255,255,.97);backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,.6);border-radius:22px;box-shadow:0 30px 70px -12px rgba(0,0,0,.5),0 1px 0 rgba(255,255,255,.6) inset;padding:38px 34px 30px}
        .logo-badge{width:58px;height:58px;border-radius:16px;background:linear-gradient(150deg,var(--gold-400),var(--gold-600));color:var(--navy-950);display:grid;place-items:center;font-size:22px;font-weight:800;font-family:'Lexend',sans-serif;margin:0 auto 16px;box-shadow:0 10px 24px rgba(227,176,78,.4),inset 0 1px 0 rgba(255,255,255,.5)}
        .school{font-family:'Lexend',sans-serif;font-weight:700;text-align:center;line-height:1.3;margin-bottom:22px;font-size:15px;color:#12162B}
        .school small{display:block;font-size:11.5px;font-weight:500;color:#8991A8;margin-top:3px;font-family:Inter,sans-serif}
        .form-label{font-size:11.5px;font-weight:700;color:#333A52;margin-bottom:5px;text-transform:uppercase;letter-spacing:.04em}
        .form-control{border-radius:11px;border-color:#E7E9F2;font-size:13.5px;padding:11px 14px;background:#FAFBFD}
        .form-control:focus{box-shadow:0 0 0 3px rgba(227,176,78,.18);border-color:var(--gold-500)}
        .btn-login{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;border:none;border-radius:11px;padding:12px;font-size:13.5px;font-weight:700;width:100%;box-shadow:0 8px 20px -4px rgba(12,19,48,.4);transition:transform .12s ease}
        .btn-login:hover{transform:translateY(-1px)}
        .tabline{margin:18px 0 12px;font-size:10.5px;color:#8991A8;display:flex;gap:8px;justify-content:center;flex-wrap:wrap}
        .sys-pill{display:inline-block;padding:3px 10px;border-radius:999px;background:#F5F6FA;border:1px solid #E7E9F2;font-weight:600}
        .forgot{display:block;text-align:center;margin-top:16px;font-size:12.5px;color:var(--navy-800);font-weight:600;text-decoration:none}
        .forgot:hover{color:var(--gold-600)}
    </style>
</head>
<body>
<div class="wrap">
    <div class="backdrop-word">NATIONAL<br>UNIVERSITY</div>
    <div class="backdrop-sub">Education that works</div>
    <div class="login-card">
        <div class="logo-badge">NU</div>
        <div class="school">NU Clark Asset Management<small>Integrated Asset, Inventory &amp; Facilities Platform</small></div>
        <form method="POST" action="{{ route('login.submit') }}">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger py-2 px-3" style="font-size:12.5px;border-radius:10px;">{{ $errors->first() }}</div>
            @endif
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="you@nuclark.local" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                <a href="{{ route('password.request') }}" style="display:inline-block;margin-top:6px;font-size:11.5px;color:#8991A8;text-decoration:none;font-weight:600">Forgot password?</a>
            </div>
            <button class="btn-login">Log in</button>
        </form>
        <div class="tabline"><span class="sys-pill">Asset Office</span><span class="sys-pill">Facilities Office</span><span class="sys-pill">Dept. User</span></div>
        <a href="{{ route('register') }}" class="forgot">Create an account →</a>
    </div>
</div>
</body>
</html>
