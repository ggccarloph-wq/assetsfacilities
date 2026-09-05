<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root{--navy-950:#080D22;--navy-900:#0C1330;--navy-800:#141B42;--navy-700:#1D2657;--gold-600:#C9932E;--gold-500:#E3B04E;--gold-400:#F0C876}
        body{min-height:100vh;margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:radial-gradient(circle at 20% 20%,rgba(227,176,78,.08),transparent 30%),linear-gradient(150deg,var(--navy-800),var(--navy-900) 55%,var(--navy-950));display:grid;place-items:center;padding:24px}
        h1,.left-pane h1{font-family:'Lexend',sans-serif}
        .card-wrap{width:900px;max-width:96vw;background:rgba(255,255,255,.98);border:1px solid rgba(255,255,255,.5);border-radius:22px;box-shadow:0 30px 70px -12px rgba(0,0,0,.5);overflow:hidden}
        .left-pane{background:linear-gradient(165deg,var(--navy-700),var(--navy-950));color:#fff;padding:32px;height:100%;position:relative}
        .left-pane::after{content:'';position:absolute;left:0;top:0;bottom:0;width:3px;background:linear-gradient(180deg,var(--gold-400),var(--gold-600))}
        .left-pane h1{font-size:26px;font-weight:700;margin-bottom:10px;letter-spacing:-.01em}
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
        .muted{font-size:12px;color:#8991A8}
        .verified-badge{display:inline-block;background:#E6F6EE;color:#0F7A4E;border-radius:999px;padding:6px 12px;font-size:12px;font-weight:700;border:1px solid #BEE7D2}
    </style>
</head>
<body>
<div class="card-wrap">
    <div class="row g-0">
        <div class="col-lg-4">
            <div class="left-pane">
                <h1>Create your account</h1>
                <p>Bago makagawa ng account, kailangan munang ma-verify ang email gamit ang code na isi-send sa inbox.</p>
                <div class="step"><strong>Step 1</strong>Enter your email, send the code, then verify the 6-digit code.</div>
                <div class="step"><strong>Step 2</strong>Choose Student Access or verify an Asset Management voucher.</div>
                <div class="step"><strong>Step 3</strong>Complete your account details to finish registration.</div>
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
                    <h5>1. Verify your email</h5>
                    @if($verifiedEmail)
                        <div class="mb-3"><span class="verified-badge">Verified: {{ $verifiedEmail }}</span></div>
                    @endif
                    <form method="POST" action="{{ route('register.send-code') }}" class="mb-3">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">Email address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $pendingVerification['email'] ?? $verifiedEmail ?? '') }}" placeholder="Enter your email" required {{ $verifiedEmail ? 'readonly' : '' }}>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-main w-100" type="submit" {{ $verifiedEmail ? 'disabled' : '' }}>{{ $pendingVerification ? 'Resend code' : 'Send code' }}</button>
                            </div>
                        </div>
                        <div class="muted mt-2">Code expires in 10 minutes.</div>
                    </form>

                    <form method="POST" action="{{ route('register.verify-code') }}">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label class="form-label">6-digit verification code</label>
                                <input type="text" name="code" class="form-control" inputmode="numeric" maxlength="6" placeholder="123456" required {{ $verifiedEmail ? 'disabled' : '' }}>
                            </div>
                            <div class="col-md-4">
                                <button class="btn-main w-100" type="submit" {{ $verifiedEmail ? 'disabled' : '' }}>Verify code</button>
                            </div>
                        </div>
                        <div class="muted mt-2">No need to enter your email again. The code will be matched to the email above.</div>
                    </form>
                </div>

                <div class="box mb-3">
                    <h5>2. Choose account access</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-bold mb-1">🎓 Student Access</div>
                                <div class="muted">No voucher required. Student accounts can access Activity Proposals for venue/activity requests.</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100">
                                <div class="fw-bold mb-1">🏢 Asset Management Access 🔒</div>
                                <div class="muted mb-3">Requestor and Approver accounts require a single-use voucher from Asset Management.</div>
                                @if($verifiedVoucher)
                                    <span class="verified-badge">Voucher verified: {{ ucfirst($verifiedVoucher['voucher_type']) }}@if(!empty($verifiedVoucher['approver_type'])) · {{ ucwords(str_replace('_',' ', $verifiedVoucher['approver_type'])) }}@endif</span>
                                @else
                                    <form method="POST" action="{{ route('register.verify-voucher') }}">
                                        @csrf
                                        <div class="input-group">
                                            <input type="text" name="voucher_code" class="form-control text-uppercase" placeholder="e.g. REQ-AB12-CD34" required>
                                            <button class="btn-main" type="submit">Verify Voucher</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box mb-0">
                    <h5>3. Finish account creation</h5>
                    <form method="POST" action="{{ route('register.submit') }}" id="registrationForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Account Access</label>
                                <select name="account_access" id="accountAccess" class="form-control" required>
                                    <option value="student" @selected(old('account_access', $verifiedVoucher ? 'asset' : 'student') === 'student')>Student Access — Activity Proposals only</option>
                                    <option value="asset" @selected(old('account_access', $verifiedVoucher ? 'asset' : '') === 'asset') @disabled(!$verifiedVoucher)>Asset Management Access — {{ $verifiedVoucher ? ucfirst($verifiedVoucher['voucher_type']) : 'voucher required' }}</option>
                                </select>
                                @if(!$verifiedVoucher)<div class="muted mt-1">Verify an Asset Management voucher above to unlock Requestor / Approver registration.</div>@endif
                            </div>
                            @if($verifiedVoucher)
                            <div class="col-12" id="assetAuthorization">
                                <div class="alert alert-info mb-0">
                                    <strong>Authorized account:</strong> {{ ucfirst($verifiedVoucher['voucher_type']) }}
                                    @if(!empty($verifiedVoucher['approver_type'])) · {{ ucwords(str_replace('_',' ', $verifiedVoucher['approver_type'])) }} Approver @endif
                                    @if(!empty($verifiedVoucher['department_id']))<br><span class="small">Department is locked to the one selected by Asset Management.</span>@endif
                                </div>
                            </div>
                            @endif
                            <div class="col-md-6">
                                <label class="form-label">Full name</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control bg-light" value="{{ $verifiedEmail ?? '' }}" placeholder="Verify your email above first" readonly required>
                                <div class="muted mt-1">This email comes from Step 1 and cannot be edited here.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Confirm password</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-control" {{ !empty($verifiedVoucher['department_id']) ? 'disabled' : '' }}>
                                    <option value="">Select your department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" @selected(old('department_id', $verifiedVoucher['department_id'] ?? null) == $department->id)>{{ $department->name }}</option>
                                    @endforeach
                                </select>
                                @if(!empty($verifiedVoucher['department_id']))
                                    <input type="hidden" name="department_id" value="{{ $verifiedVoucher['department_id'] }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Access Summary</label>
                                <div class="form-control bg-light" id="accessSummary" style="height:auto;min-height:43px"></div>
                            </div>
                            <div class="col-12">
                                <div class="muted mb-2" id="approvalNote"></div>
                                <button class="btn-main" type="submit">Create account</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const access = document.getElementById('accountAccess');
    const summary = document.getElementById('accessSummary');
    const note = document.getElementById('approvalNote');
    const voucherType = @json($verifiedVoucher['voucher_type'] ?? null);
    const approverType = @json($verifiedVoucher['approver_type'] ?? null);
    function sync() {
        if (access.value === 'student') {
            summary.textContent = 'Student · Activity Proposals only';
            note.textContent = 'Student accounts are activated after verified-email registration and are managed on the Facilities side.';
            return;
        }
        if (voucherType === 'approver') {
            summary.textContent = 'Approver · ' + (approverType || '').replaceAll('_', ' ') + ' · Asset Management';
            note.textContent = 'Approver type comes from the voucher and cannot be changed. This voucher-authorized account is active immediately after registration.';
        } else {
            summary.textContent = 'Requestor · OPEX + Requisition + Activity Proposals';
            note.textContent = 'This voucher-authorized Asset Management requestor account is activated after verified-email registration.';
        }
    }
    access.addEventListener('change', sync); sync();
})();
</script>
</body>
</html>
