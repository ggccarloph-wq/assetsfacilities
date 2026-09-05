<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--navy-950:#080D22;--navy-900:#0C1330;--navy-800:#141B42;--navy-700:#1D2657;--gold-600:#C9932E;--gold-500:#E3B04E;--gold-400:#F0C876}
        body{min-height:100vh;margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 20% 20%,rgba(227,176,78,.08),transparent 30%),linear-gradient(150deg,var(--navy-800),var(--navy-900) 55%,var(--navy-950));display:grid;place-items:center;padding:24px}
        h1,.left-pane h1{font-family:'Lexend',sans-serif}
        .card-wrap{width:820px;max-width:96vw;background:rgba(255,255,255,.98);border:1px solid rgba(255,255,255,.5);border-radius:22px;box-shadow:0 30px 70px -12px rgba(0,0,0,.5);overflow:hidden}
        .left-pane{background:linear-gradient(165deg,var(--navy-700),var(--navy-950));color:#fff;padding:32px;height:100%;position:relative}
        .left-pane::after{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--gold-400),var(--gold-600))}
        .left-pane h1{font-size:24px;font-weight:700;margin-bottom:10px;letter-spacing:-.01em}
        .left-pane p{font-size:13.5px;opacity:.85;line-height:1.6}
        .step{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:14px 16px;margin-top:12px}
        .step strong{display:block;font-size:12.5px;margin-bottom:4px;color:var(--gold-400);text-transform:uppercase;letter-spacing:.04em}
        .right-pane{padding:30px}
        .box{border:1px solid #E7E9F2;border-radius:16px;padding:20px;margin-bottom:16px;background:#FAFBFD}
        .box h5{font-size:15px;font-weight:700;margin-bottom:14px;font-family:'Lexend',sans-serif;color:#12162B}
        .form-label{font-size:11.5px;font-weight:700;color:#333A52;text-transform:uppercase;letter-spacing:.03em}
        .form-control{border-radius:10px;padding:10px 12px;border-color:#E7E9F2}
        .form-control:focus{box-shadow:0 0 0 3px rgba(227,176,78,.16);border-color:var(--gold-500)}
        .btn-main{background:linear-gradient(155deg,var(--navy-700),var(--navy-900));color:#fff;border:none;border-radius:10px;padding:10px 16px;font-size:13px;font-weight:700;box-shadow:0 6px 16px -4px rgba(12,19,48,.4)}
        .btn-main:hover{color:#fff;opacity:.95}
        .btn-main:disabled{opacity:.5;cursor:not-allowed}
        .muted{font-size:12px;color:#8991A8}
        .verified-badge{display:inline-block;background:#E6F6EE;color:#0F7A4E;border-radius:999px;padding:6px 12px;font-size:12px;font-weight:700;border:1px solid #BEE7D2}
    </style>
</head>
<body>
<div class="card-wrap">
    <div class="row g-0">
        <div class="col-lg-4">
            <div class="left-pane">
                <h1>Reset your password</h1>
                <p>Para sa seguridad, kailangan munang ma-verify ang access mo sa email bago makapagtakda ng bagong password.</p>
                <div class="step"><strong>Step 1</strong>Enter your registered email and send a reset code.</div>
                <div class="step"><strong>Step 2</strong>Type the 6-digit code from your email.</div>
                <div class="step"><strong>Step 3</strong>Set your new password.</div>
                <div class="mt-4"><a href="{{ route('login') }}" class="btn btn-light btn-sm">Back to login</a></div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="right-pane">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="box">
                    <h5>1. Send reset code</h5>
                    <form method="POST" action="{{ route('password.send-code') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Registered email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', old('verified_email', $verifiedEmail ?? '')) }}" placeholder="Enter your account email" required>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-main w-100" type="submit">Send code</button>
                            </div>
                        </div>
                        <div class="muted mt-2">Code expires in 10 minutes.</div>
                    </form>
                </div>

                <div class="box">
                    <h5>2. Verify reset code</h5>
                    @if($verifiedEmail)
                        <div class="mb-3"><span class="verified-badge">Verified: {{ $verifiedEmail }}</span></div>
                    @endif
                    <form method="POST" action="{{ route('password.verify-code') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', old('verified_email', $verifiedEmail ?? ($pendingVerification['email'] ?? ''))) }}" placeholder="Same email as above" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">6-digit code</label>
                                <input type="text" name="code" class="form-control" maxlength="6" placeholder="123456" required>
                            </div>
                            <div class="col-md-3">
                                <button class="btn-main w-100" type="submit">Verify code</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="box mb-0">
                    <h5>3. Set new password</h5>
                    <form method="POST" action="{{ route('password.reset') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Verified email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('verified_email', $verifiedEmail ?? '') }}" placeholder="Verify your email above first" required @if(!$verifiedEmail) readonly @endif>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">New password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm new password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter new password" required>
                            </div>
                            <div class="col-12">
                                <div class="muted mb-2">You can only set a new password after verifying the code sent to your email above.</div>
                                <button class="btn-main" type="submit" @if(!$verifiedEmail) disabled @endif>Reset password</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
