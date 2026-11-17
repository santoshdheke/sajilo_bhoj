<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Redirecting to eSewa…</title>
    <style>
        body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; font-family:Inter, system-ui, sans-serif; color:#1c2430; background:#f3f8f4; }
        .box { text-align:center; } .spin { width:42px; height:42px; margin:0 auto 14px; border:4px solid #d9eedb; border-top-color:#60bb46; border-radius:50%; animation:s 1s linear infinite; }
        @keyframes s { to { transform:rotate(360deg); } } button { margin-top:14px; border:0; background:#60bb46; color:#fff; padding:10px 18px; border-radius:10px; font-weight:600; cursor:pointer; }
    </style>
</head>
<body>
    <form id="esewa" method="POST" action="{{ $action }}" class="box">
        <div class="spin"></div>
        <div>Taking you to eSewa to complete the payment…</div>
        @foreach ($fields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach
        <noscript><button type="submit">Continue to eSewa</button></noscript>
    </form>
    <script>document.getElementById('esewa').submit();</script>
</body>
</html>
