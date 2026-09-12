<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | NU Clark Asset Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Lexend:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ==========================================================================
           Registration — 2026 rebuild
           Replaces the old three-stacked-grey-boxes layout with a two-pane shell:
           a navy progress rail on the left and a vertical timeline of steps on the
           right. Field names, routes and partials are unchanged.
           ========================================================================== */
        :root{
            --navy-950:#080D22;--navy-900:#0C1330;--navy-800:#141B42;--navy-700:#1D2657;
            --gold-600:#C9932E;--gold-500:#E3B04E;--gold-400:#F0C876;
            --blue-600:#1C56AE;--blue-500:#2E6AC9;
            --ink-900:#131E33;--ink-600:#46556F;--ink-400:#78879F;
            --line:#D3DEEC;--line-soft:#E4EBF4;--surface:#FFFFFF;--canvas:#EDF2F9;
        }
        *{box-sizing:border-box}
        body{
            min-height:100vh;margin:0;padding:34px 20px;font-family:Inter,Segoe UI,Arial,sans-serif;color:var(--ink-900);
            background:
                radial-gradient(760px 460px at 14% 6%,rgba(46,106,201,.30),transparent 62%),
                radial-gradient(620px 420px at 90% 96%,rgba(227,176,78,.14),transparent 62%),
                linear-gradient(150deg,var(--navy-700) 0%,var(--navy-900) 52%,var(--navy-950) 100%);
            display:grid;place-items:center;-webkit-font-smoothing:antialiased;
        }
        h1,h2,.rail-brand-name{font-family:'Lexend',sans-serif;letter-spacing:-.02em}

        .shell{
            width:1060px;max-width:100%;display:grid;grid-template-columns:330px minmax(0,1fr);
            background:var(--surface);border:1px solid rgba(255,255,255,.7);border-radius:26px;overflow:hidden;
            box-shadow:0 44px 100px -28px rgba(4,10,28,.66);
        }

        /* ---------- Left rail ---------- */
        .rail{
            position:relative;padding:34px 28px;color:#fff;
            background:linear-gradient(168deg,var(--navy-700) 0%,var(--navy-900) 52%,var(--navy-950) 100%);
        }
        .rail::after{content:'';position:absolute;right:-70px;top:-60px;width:220px;height:220px;border-radius:50%;background:radial-gradient(circle,rgba(227,176,78,.20),transparent 70%);pointer-events:none}
        .rail-brand{display:flex;align-items:center;gap:11px;margin-bottom:26px;position:relative;z-index:1}
        .rail-brand-mark{width:42px;height:42px;border-radius:13px;display:grid;place-items:center;font-family:'Lexend',sans-serif;font-weight:800;font-size:15px;color:var(--navy-950);background:linear-gradient(150deg,var(--gold-400),var(--gold-600));box-shadow:0 8px 20px rgba(227,176,78,.32)}
        .rail-brand-name{font-size:13px;font-weight:700;line-height:1.2}
        .rail-brand-sub{font-size:9.5px;letter-spacing:.09em;text-transform:uppercase;color:#9FADD4;font-weight:700;margin-top:2px}
        .rail h1{font-size:25px;font-weight:700;margin:0 0 9px;position:relative;z-index:1}
        .rail-lede{font-size:12.5px;line-height:1.65;color:#B4C0DE;margin:0 0 26px;position:relative;z-index:1}

        .rail-steps{position:relative;display:grid;gap:2px;z-index:1}
        .rail-step{position:relative;display:flex;gap:13px;padding:0 0 20px}
        .rail-step:last-child{padding-bottom:0}
        .rail-step::before{content:'';position:absolute;left:14px;top:32px;bottom:2px;width:2px;background:rgba(255,255,255,.12);border-radius:2px}
        .rail-step:last-child::before{display:none}
        .rail-step.done::before{background:linear-gradient(180deg,var(--gold-500),rgba(227,176,78,.18))}
        .rail-dot{
            position:relative;z-index:1;width:29px;height:29px;min-width:29px;border-radius:50%;display:grid;place-items:center;
            font-size:11px;font-weight:800;color:#8E9CC4;background:rgba(255,255,255,.07);border:1.5px solid rgba(255,255,255,.16);
        }
        .rail-step.active .rail-dot{color:#fff;background:linear-gradient(150deg,var(--blue-500),var(--blue-600));border-color:rgba(255,255,255,.35);box-shadow:0 0 0 5px rgba(46,106,201,.18)}
        .rail-step.done .rail-dot{color:var(--navy-950);background:linear-gradient(150deg,var(--gold-400),var(--gold-600));border-color:transparent}
        .rail-step-copy strong{display:block;font-size:12.5px;font-weight:700;color:#E7ECF9;margin-bottom:3px}
        .rail-step-copy span{display:block;font-size:11.5px;line-height:1.55;color:#93A1C6}
        .rail-step.done .rail-step-copy strong{color:#fff}

        .rail-foot{margin-top:30px;padding-top:20px;border-top:1px solid rgba(255,255,255,.1);position:relative;z-index:1}
        .rail-back{display:inline-flex;align-items:center;gap:7px;font-size:12px;font-weight:700;color:#C3CEEA;text-decoration:none;padding:9px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.16);transition:.15s ease}
        .rail-back:hover{color:#fff;background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.3)}

        /* ---------- Right: step timeline ---------- */
        .pane{padding:34px 36px}
        .pane-head{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:26px}
        .pane-eyebrow{font-size:10px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--blue-600);margin-bottom:5px}
        .pane-head h2{font-size:21px;font-weight:700;margin:0;color:var(--ink-900)}
        .pane-head p{margin:6px 0 0;font-size:12.5px;color:var(--ink-400);line-height:1.55}
        .pane-progress{white-space:nowrap;border:1.5px solid var(--line);background:#F6F9FD;border-radius:999px;padding:8px 14px;font-size:11px;font-weight:800;color:#3E5C8C}

        .flow{display:grid}
        .flow-step{position:relative;display:grid;grid-template-columns:34px minmax(0,1fr);gap:16px;padding-bottom:26px}
        .flow-step:last-child{padding-bottom:0}
        .flow-step::before{content:'';position:absolute;left:16px;top:38px;bottom:0;width:2px;background:var(--line-soft)}
        .flow-step:last-child::before{display:none}
        .flow-step.done::before{background:linear-gradient(180deg,#8FBF9F,var(--line-soft))}
        .flow-marker{
            width:34px;height:34px;border-radius:11px;display:grid;place-items:center;font-family:'Lexend',sans-serif;
            font-size:12px;font-weight:800;color:#fff;background:linear-gradient(150deg,var(--navy-700),var(--navy-900));
            box-shadow:0 7px 16px rgba(20,27,66,.24);
        }
        .flow-step.done .flow-marker{background:linear-gradient(150deg,#2FA365,#177A45);box-shadow:0 7px 16px rgba(23,122,69,.24)}
        .flow-title{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin:5px 0 3px}
        .flow-title h3{font-family:'Lexend',sans-serif;font-size:15px;font-weight:700;margin:0;color:var(--ink-900);letter-spacing:-.01em}
        .flow-sub{margin:0 0 15px;font-size:12px;color:var(--ink-400);line-height:1.55;max-width:560px}
        .flow-card{border:1.5px solid var(--line);border-radius:16px;background:linear-gradient(180deg,#FFFFFF,#FAFCFE);padding:19px 19px 17px;box-shadow:0 6px 18px rgba(23,52,102,.05)}
        .flow-step.active .flow-card{border-color:#B7CCE8;box-shadow:0 10px 26px rgba(23,52,102,.09)}

        .tag{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 11px;font-size:10px;font-weight:800;letter-spacing:.04em;text-transform:uppercase}
        .tag-done{background:#E4F6EC;color:#12703F;border:1px solid #B4E2C7}
        .tag-optional{background:#F1F4F9;color:#5E6E88;border:1px solid #DCE3EE;text-transform:none;letter-spacing:.01em;font-size:10.5px}
        .tag-locked{background:#FDF3E3;color:#9A6E14;border:1px solid #F0DDB6}

        /* ---------- Fields ---------- */
        .form-label{font-size:10.5px;font-weight:800;letter-spacing:.07em;text-transform:uppercase;color:#4B5E7E;margin-bottom:6px}
        .form-control,.form-select{
            min-height:47px;border:1.5px solid #C3D2E5;border-radius:12px;background:#fff;color:var(--ink-900);
            font-size:13px;font-weight:600;padding:11px 13px;transition:border-color .15s ease,box-shadow .15s ease,background .15s ease;
        }
        .form-control::placeholder{color:#93A2B8;font-weight:500;opacity:1}
        .form-control:hover:not([readonly]):not(:disabled),.form-select:hover:not(:disabled){border-color:#93AFD3}
        .form-control:focus,.form-select:focus{border-color:var(--blue-600);background:#fff;box-shadow:0 0 0 4px rgba(28,86,174,.13);outline:0}
        .form-control[readonly],.form-control:disabled,.form-select:disabled{background:#F1F5FA;color:#5E6E88;border-color:#D5DFEC;cursor:not-allowed}
        .hint{font-size:11px;color:var(--ink-400);line-height:1.5;margin-top:7px}
        .hint-strong{color:#3E5C8C;font-weight:700}

        .btn-main{
            display:inline-flex;align-items:center;justify-content:center;gap:8px;white-space:nowrap;
            background:linear-gradient(150deg,var(--blue-500),#153F84);color:#fff;border:none;border-radius:12px;
            padding:12px 20px;min-height:47px;font-size:12.5px;font-weight:800;letter-spacing:.01em;
            box-shadow:0 10px 22px -8px rgba(21,63,132,.6);transition:.15s ease;
        }
        .btn-main:hover:not(:disabled){color:#fff;transform:translateY(-1px);box-shadow:0 14px 26px -8px rgba(21,63,132,.65)}
        .btn-main:disabled{opacity:.45;box-shadow:none;cursor:not-allowed}
        .btn-submit{width:100%;min-height:52px;font-size:13.5px;border-radius:14px}

        .field-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:11px;align-items:end}
        .voucher-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:11px}
        .voucher-row .form-control{letter-spacing:.12em;font-weight:700;text-transform:uppercase}

        .callout{display:flex;gap:11px;align-items:flex-start;border-radius:13px;padding:13px 15px;font-size:11.5px;line-height:1.55}
        .callout-info{background:#EEF4FD;border:1.5px solid #C6DAF3;color:#2C4A78}
        .callout-ok{background:#EDF9F2;border:1.5px solid #B4E2C7;color:#15633B}
        .callout strong{display:block;font-weight:800;margin-bottom:2px}
        .callout .dot{width:8px;height:8px;min-width:8px;border-radius:50%;margin-top:4px;background:currentColor;opacity:.55}

        .access-chip{display:flex;align-items:center;gap:12px;border:1.5px solid #C6DAF3;background:linear-gradient(140deg,#F6FAFF,#EBF3FE);border-radius:14px;padding:13px 15px;margin-bottom:17px}
        .access-chip-icon{width:36px;height:36px;min-width:36px;border-radius:11px;display:grid;place-items:center;background:linear-gradient(150deg,var(--blue-500),#153F84);color:#fff;font-size:15px;font-weight:800}
        .access-chip-copy{min-width:0}
        .access-chip-copy span{display:block;font-size:9.5px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:#6C7F9B}
        .access-chip-copy strong{display:block;font-size:13px;font-weight:800;color:#1E3A66;margin-top:2px}

        .signature-block{border:1.5px solid var(--line);border-radius:14px;background:#fff;padding:16px}
        .signature-block .sig-title{font-size:12.5px;font-weight:800;color:var(--ink-900);margin-bottom:3px}

        .divider{height:1px;background:var(--line-soft);margin:17px 0}

        /* Code row revealed after "Send code". */
        .code-reveal{margin-top:17px;padding-top:17px;border-top:1px dashed #C9D8EA;animation:revealDown .28s cubic-bezier(.22,1,.36,1)}
        @keyframes revealDown{from{opacity:0;transform:translateY(-7px)}to{opacity:1;transform:none}}
        .code-sent{display:flex;gap:10px;align-items:flex-start;margin-bottom:14px;font-size:11.5px;line-height:1.5;color:#2C4A78}
        .code-sent .dot{width:8px;height:8px;min-width:8px;border-radius:50%;margin-top:4px;background:#2FA365}
        .code-sent strong{font-weight:800;color:#1E3A66}
        .code-input{letter-spacing:.42em;font-weight:800;font-size:16px}
        @media (prefers-reduced-motion:reduce){.code-reveal{animation:none}}

        /* ---------- Responsive ---------- */
        @media (max-width:991.98px){
            body{display:block;padding:16px 12px}
            .shell{grid-template-columns:1fr;margin:0 auto;border-radius:20px}
            .rail{padding:26px 22px}
            .rail h1{font-size:22px}
            .pane{padding:26px 22px}
            .pane-head{flex-direction:column}
        }
        @media (max-width:575.98px){
            body{padding:0}
            .shell{border-radius:0;border:0;min-height:100dvh;box-shadow:none}
            .rail{padding:24px 18px}
            .rail h1{font-size:20px}
            .pane{padding:22px 16px}
            .pane-head h2{font-size:19px}
            .flow-step{grid-template-columns:28px minmax(0,1fr);gap:12px}
            .flow-step::before{left:13px;top:33px}
            .flow-marker{width:28px;height:28px;border-radius:9px;font-size:11px}
            .flow-card{padding:15px;border-radius:14px}
            .form-control,.form-select{font-size:16px;min-height:50px}
            .field-row,.voucher-row{grid-template-columns:1fr}
            .btn-main{width:100%}
            .row>[class*="col-"]:not(.col-auto){width:100%;max-width:100%;flex:0 0 100%}
        }
    </style>
</head>
<body>
@php
    // Access level is no longer something the applicant picks — it is decided by
    // whether Asset Management issued them a voucher. Student registration is
    // simply "no voucher", so the old dropdown and summary field are gone.
    $isAsset      = (bool) $verifiedVoucher;
    $voucherType  = $verifiedVoucher['voucher_type'] ?? null;
    $approverType = $verifiedVoucher['approver_type'] ?? null;
    $accessTitle  = $isAsset
        ? ($voucherType === 'approver'
            ? 'Approver — '.ucwords(str_replace('_', ' ', (string) $approverType))
            : 'Requestor — Asset Management')
        : 'Student — Activity Proposals';
    $accessNote   = $isAsset
        ? ($voucherType === 'approver'
            ? 'Your approver type comes from the voucher and cannot be changed here. The account is active immediately after registration.'
            : 'This voucher-authorized requestor account can file charge slips, OPEX requisitions and activity proposals.')
        : 'Student accounts are activated after verified-email registration and are managed on the Facilities side.';
@endphp

<div class="shell">
    <aside class="rail">
        <div class="rail-brand">
            <div class="rail-brand-mark">NU</div>
            <div>
                <div class="rail-brand-name">National University</div>
                <div class="rail-brand-sub">Clark — Asset Management</div>
            </div>
        </div>

        <h1>Create your account</h1>
        <p class="rail-lede">Kailangan munang ma-verify ang email bago makagawa ng account. Kung may voucher ka mula sa Asset Management, i-verify mo rin ito para ma-unlock ang Requestor o Approver access.</p>

        <div class="rail-steps">
            <div class="rail-step {{ $verifiedEmail ? 'done' : 'active' }}">
                <div class="rail-dot">{!! $verifiedEmail ? '&check;' : '1' !!}</div>
                <div class="rail-step-copy">
                    <strong>Email verification</strong>
                    <span>Send a code to your inbox, then enter the 6 digits.</span>
                </div>
            </div>
            <div class="rail-step {{ $isAsset ? 'done' : ($verifiedEmail ? 'active' : '') }}">
                <div class="rail-dot">{!! $isAsset ? '&check;' : '2' !!}</div>
                <div class="rail-step-copy">
                    <strong>Account access</strong>
                    <span>Voucher check for Requestor / Approver. Skip if student.</span>
                </div>
            </div>
            <div class="rail-step {{ $verifiedEmail ? 'active' : '' }}">
                <div class="rail-dot">3</div>
                <div class="rail-step-copy">
                    <strong>Account details</strong>
                    <span>Name, password and department to finish up.</span>
                </div>
            </div>
        </div>

        <div class="rail-foot">
            <a href="{{ route('login') }}" class="rail-back">&larr; Back to login</a>
        </div>
    </aside>

    <main class="pane">
        <div class="pane-head">
            <div>
                <div class="pane-eyebrow">Registration</div>
                <h2>Set up your access</h2>
                <p>Three short steps. Your access level is set automatically from your voucher.</p>
            </div>
            <div class="pane-progress">{{ $verifiedEmail ? ($isAsset ? 'Step 3 of 3' : 'Step 2 of 3') : 'Step 1 of 3' }}</div>
        </div>

        <div class="flow">
            {{-- ---------------- Step 1 ---------------- --}}
            <section class="flow-step {{ $verifiedEmail ? 'done' : 'active' }}">
                <div class="flow-marker">{!! $verifiedEmail ? '&check;' : '1' !!}</div>
                <div>
                    <div class="flow-title">
                        <h3>Verify your email</h3>
                        @if($verifiedEmail)<span class="tag tag-done">Verified</span>@endif
                    </div>
                    <p class="flow-sub">We send a 6-digit code to your inbox. The code expires in 10 minutes.</p>

                    <div class="flow-card">
                        @if($verifiedEmail)
                            <div class="callout callout-ok">
                                <span class="dot"></span>
                                <div><strong>{{ $verifiedEmail }}</strong>This address is confirmed and will be used for your account.</div>
                            </div>
                        @else
                            {{-- One field to start with. The code row is only rendered
                                 once a code has actually been sent, so the step reads as
                                 a single action instead of two competing forms. --}}
                            <form method="POST" action="{{ route('register.send-code') }}">
                                @csrf
                                <div class="field-row">
                                    <div>
                                        <label class="form-label">Email address</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email', $pendingVerification['email'] ?? '') }}" placeholder="Enter your email" required>
                                    </div>
                                    <button class="btn-main {{ $pendingVerification ? 'btn-ghost' : '' }}" type="submit">{{ $pendingVerification ? 'Resend code' : 'Send code' }}</button>
                                </div>
                            </form>

                            @if($pendingVerification)
                                <div class="code-reveal">
                                    <div class="code-sent">
                                        <span class="dot"></span>
                                        <div>Code sent to <strong>{{ $pendingVerification['email'] }}</strong>. Check your inbox, then enter it below.</div>
                                    </div>
                                    <form method="POST" action="{{ route('register.verify-code') }}">
                                        @csrf
                                        <div class="field-row">
                                            <div>
                                                <label class="form-label">Enter code</label>
                                                <input type="text" name="code" class="form-control code-input" inputmode="numeric" maxlength="6" placeholder="123456" autocomplete="one-time-code" autofocus required>
                                            </div>
                                            <button class="btn-main" type="submit">Verify code</button>
                                        </div>
                                        <div class="hint">No need to retype your email — the code is matched to the address above.</div>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </section>

            {{-- ---------------- Step 2 ---------------- --}}
            <section class="flow-step {{ $isAsset ? 'done' : ($verifiedEmail ? 'active' : '') }}">
                <div class="flow-marker">{!! $isAsset ? '&check;' : '2' !!}</div>
                <div>
                    <div class="flow-title">
                        <h3>Verify account access</h3>
                        @if($isAsset)
                            <span class="tag tag-done">Voucher verified</span>
                        @else
                            <span class="tag tag-optional">Skip this if you're a student</span>
                        @endif
                    </div>
                    <p class="flow-sub">Asset Management account verification.</p>

                    <div class="flow-card">
                        @if($isAsset)
                            <div class="access-chip" style="margin-bottom:0">
                                <div class="access-chip-icon">&#10003;</div>
                                <div class="access-chip-copy">
                                    <span>Authorized account</span>
                                    <strong>{{ $accessTitle }}</strong>
                                </div>
                            </div>
                            @if(!empty($verifiedVoucher['department_id']))
                                <div class="hint">Your department is locked to the one selected by Asset Management.</div>
                            @endif
                        @else
                            <div class="hint" style="margin-top:0;margin-bottom:13px">Requestor and Approver accounts require a valid voucher code issued by Asset Management.</div>
                            <form method="POST" action="{{ route('register.verify-voucher') }}">
                                @csrf
                                <label class="form-label">Voucher code</label>
                                <div class="voucher-row">
                                    <input type="text" name="voucher_code" class="form-control" placeholder="Enter voucher code" autocomplete="off" required>
                                    <button class="btn-main" type="submit">Verify Voucher</button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </section>

            {{-- ---------------- Step 3 ---------------- --}}
            <section class="flow-step {{ $verifiedEmail ? 'active' : '' }}">
                <div class="flow-marker">3</div>
                <div>
                    <div class="flow-title">
                        <h3>Finish account creation</h3>
                        @unless($verifiedEmail)<span class="tag tag-locked">Verify email first</span>@endunless
                    </div>
                    <p class="flow-sub">These details are printed on your requests and used to sign you in.</p>

                    <div class="flow-card">
                        <div class="access-chip">
                            <div class="access-chip-icon">{{ $isAsset ? ($voucherType === 'approver' ? 'A' : 'R') : 'S' }}</div>
                            <div class="access-chip-copy">
                                <span>Account access</span>
                                <strong>{{ $accessTitle }}</strong>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('register.submit') }}" id="registrationForm" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full name</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter your full name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $verifiedEmail ?? '' }}" placeholder="Verify your email above first" readonly required>
                                    <div class="hint">Carried over from Step 1 and cannot be edited here.</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="8+ characters, 1 number, 1 symbol" data-pw-rules required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Confirm password</label>
                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Department</label>
                                    <select name="department_id" class="form-select" {{ !empty($verifiedVoucher['department_id']) ? 'disabled' : '' }}>
                                        <option value="">Select your department</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" @selected(old('department_id', $verifiedVoucher['department_id'] ?? null) == $department->id)>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                    @if(!empty($verifiedVoucher['department_id']))
                                        <input type="hidden" name="department_id" value="{{ $verifiedVoucher['department_id'] }}">
                                        <div class="hint">Locked by your voucher.</div>
                                    @endif
                                </div>

                                @if($voucherType === 'approver')
                                <div class="col-12" id="approverSignatureSection">
                                    <div class="signature-block">
                                        <div class="sig-title">Required e-signature</div>
                                        <div class="hint" style="margin-top:0;margin-bottom:13px">This signature is locked after account creation and appears beside your printed name on approved proposals and receipts.</div>
                                        @include('partials.signature-input', ['signatureId' => 'registration-signature', 'signatureRequired' => true])
                                    </div>
                                </div>
                                @endif

                                <div class="col-12">
                                    <div class="callout callout-info" style="margin-bottom:14px">
                                        <span class="dot"></span>
                                        <div>{{ $accessNote }}</div>
                                    </div>
                                    <button class="btn-main btn-submit" type="submit">Create account</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </main>
</div>

@include('auth.partials.password-toggle')
@include('partials.alerts')
</body>
</html>