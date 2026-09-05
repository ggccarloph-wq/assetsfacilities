<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>500 - Server Error</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#f4f5f7; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card-box { background:#fff; border-radius:14px; padding:40px; max-width:640px; width:92%; box-shadow:0 6px 24px rgba(0,0,0,.08); text-align:center; }
        .card-box i { font-size:42px; color:#b91c1c; }
        h1 { font-size:22px; margin:14px 0 6px; }
        p.reason { color:#444; background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:12px 14px; margin:16px 0; text-align:left; font-family:monospace; font-size:13px; word-break:break-word; }
        a.btn-back { display:inline-block; margin-top:10px; padding:10px 20px; border-radius:10px; background:#1e1b4b; color:#fff; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card-box">
        <i class="bi bi-exclamation-octagon"></i>
        <h1>Something went wrong</h1>
        <p>An unexpected server error occurred while processing your request.</p>
        @auth
            @if(auth()->user()->isAdmin())
                <p class="reason">
                    {{ get_class($exception) }}: {{ $exception->getMessage() }}<br>
                    @if(method_exists($exception, 'getFile'))
                        {{ basename($exception->getFile()) }}:{{ $exception->getLine() }}
                    @endif
                </p>
                <div style="font-size:12px;color:#888;margin-top:-8px;margin-bottom:10px;">
                    (Only visible to Admin/Super Admin accounts. If this mentions "Unknown column" or "no such column", run <code>php artisan migrate</code> on the server.)
                </div>
            @endif
        @endauth
        <a class="btn-back" href="{{ url()->previous() }}">Go Back</a>
    </div>
</body>
</html>
