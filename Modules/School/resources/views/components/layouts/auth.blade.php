{{-- Centered card on a soft background: sign in, register, "awaiting approval". --}}
@props(['title' => null, 'wide' => false, 'footer' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ? "$title - " : '' }}{{ config('app.name') }}</title>

        <!-- MDBootstrap + Material theme (see project CLAUDE.md — swap for the npm/Vite build once the asset pipeline is wired up) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8/css/mdb.min.css" rel="stylesheet">
        <link href="{{ asset('school/css/sms-material.css') }}?v={{ @filemtime(public_path('school/css/sms-material.css')) }}" rel="stylesheet">
    </head>

    <body>
        <div class="sms-auth">
            <div class="sms-paper sms-auth__card {{ $wide ? 'sms-auth__card--wide' : '' }}">
                {{ $slot }}
            </div>

            @if ($footer)
                <p class="sms-auth__footer">{{ $footer }}</p>
            @endif
        </div>

        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8/js/mdb.umd.min.js"></script>
    </body>
</html>
