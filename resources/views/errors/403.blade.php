<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>403 - Not Allowed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        body { font-family: -apple-system, Segoe UI, Roboto, Arial, sans-serif; background:#f4f5f7; display:flex; align-items:center; justify-content:center; min-height:100vh; margin:0; }
        .card-box { background:#fff; border-radius:14px; padding:40px; max-width:520px; width:92%; box-shadow:0 6px 24px rgba(0,0,0,.08); text-align:center; }
        .card-box i { font-size:42px; color:#b45309; }
        h1 { font-size:22px; margin:14px 0 6px; }
        p.reason { color:#444; background:#fff7ed; border:1px solid #fed7aa; border-radius:10px; padding:12px 14px; margin:16px 0; }
        a.btn-back { display:inline-block; margin-top:10px; padding:10px 20px; border-radius:10px; background:#1e1b4b; color:#fff; text-decoration:none; }
    </style>
</head>
<body>
    <div class="card-box">
        <i class="bi bi-shield-exclamation"></i>
        <h1>Action Not Allowed</h1>
        <p>You don't have permission to do that right now.</p>
        @if($exception->getMessage())
            <p class="reason">{{ $exception->getMessage() }}</p>
        @endif
        <a class="btn-back" href="{{ url()->previous() }}">Go Back</a>
    </div>
</body>
</html>
