{{-- Shared <head> content of the shop sign-up / login / set-password pages (after <title>). --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 48 48'%3E%3Crect width='48' height='48' rx='12' fill='%232563eb'/%3E%3Cpath d='M12 20l2-7h20l2 7M14 23v13h20V23' fill='none' stroke='white' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>:root { --primary: #2563eb; }</style>
    @include('shop::partials.auth-split.styles')
    <style>
        .ws-form .form-check-input:checked { background-color: var(--primary); border-color: var(--primary); }
        .ws-form .form-check-input:focus { box-shadow: 0 0 0 4px color-mix(in srgb, var(--primary) 14%, transparent); border-color: var(--primary); }
        .ws-eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: none; color: #7a867f; padding: 6px 8px; }
        .ws-field.has-eye .form-control { padding-right: 46px; }
        .ws-form { display: flex; flex-direction: column; justify-content: center; }
        .ws-secure { display: flex; align-items: center; justify-content: center; gap: 8px; margin-top: 18px; font-size: .85rem; color: var(--muted); }
        .ws-secure i { color: var(--primary); }
    </style>
