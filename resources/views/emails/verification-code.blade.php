{{--
    Owner verification code email. Built with tables + inline styles so it looks the same in
    Gmail, Outlook and phone mail apps. Colours ($c) come from the restaurant's brand colour;
    icons are PNGs drawn in that colour and attached inline ($message->embedData).
--}}
@php
    $img = fn (string $key, string $file) => $message->embedData($icons[$key], $file, 'image/png');
    $font = "font-family:'Inter','Segoe UI',Roboto,Helvetica,Arial,sans-serif;";
    // Long restaurant names get a smaller logo font so they still fit beside the mark.
    $nameSize = mb_strlen($brand) > 22 ? 20 : (mb_strlen($brand) > 14 ? 24 : 30);
@endphp
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Your verification code</title>
    <style>
        /* Phones (mail apps that support <style>; others keep the desktop layout) */
        @media (max-width: 480px) {
            .px { padding-left: 22px !important; padding-right: 22px !important; }
            .outer { padding: 20px 10px !important; }
            .brand-name { font-size: {{ min($nameSize, 22) }}px !important; }
            .code { font-size: 30px !important; letter-spacing: 6px !important; padding: 12px 18px 12px 14px !important; }
            .code-ico { padding: 12px 10px 12px 16px !important; }
            .hi { font-size: 20px !important; }
            .lead { font-size: 16px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background:{{ $c['page'] }};{{ $font }}">
    {{-- Inbox preview text --}}
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">Your code is {{ $code }}. It works for {{ $minutes }} minutes.</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:{{ $c['page'] }};">
        <tr><td align="center" class="outer" style="padding:40px 16px;">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                   style="max-width:560px;background:#ffffff;border-radius:24px;box-shadow:0 10px 40px rgba(20,40,30,.08);">

                {{-- Logo: the website's logo mark + name (same as partials/brand-logo) --}}
                <tr><td align="center" style="padding:30px 32px 20px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                        <td style="vertical-align:middle;padding-right:12px;"><img src="{{ $img('logo', 'logo.png') }}" width="96" height="60" alt="" style="display:block;border:0;"></td>
                        <td class="brand-name" style="vertical-align:middle;{{ $font }}font-size:{{ $nameSize }}px;line-height:1.15;font-weight:800;letter-spacing:-.6px;color:{{ $c['name'] }};">{{ $brand }}</td>
                    </tr></table>
                </td></tr>
                <tr><td style="padding:0 32px;"><div style="height:2px;background:{{ $c['line'] }};line-height:2px;font-size:0;">&nbsp;</div></td></tr>

                {{-- Greeting --}}
                <tr><td class="px hi" style="padding:30px 36px 8px;{{ $font }}font-size:22px;font-weight:700;color:#1c2430;">Hi {{ $name }},</td></tr>
                <tr><td class="px lead" style="padding:0 36px 24px;{{ $font }}font-size:17px;line-height:1.55;color:#334155;">
                    Here is your code to verify your email and finish setting up your restaurant:
                </td></tr>

                {{-- Code box --}}
                <tr><td align="center" class="px" style="padding:0 36px 26px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                           style="background:{{ $c['tint'] }};border:2px solid {{ $c['border'] }};border-radius:18px;">
                        <tr>
                            <td class="code-ico" style="padding:16px 18px 16px 26px;vertical-align:middle;"><img src="{{ $img('shield', 'shield.png') }}" width="34" height="38" alt="" style="display:block;border:0;"></td>
                            <td style="vertical-align:middle;"><div style="width:2px;height:40px;background:{{ $c['border'] }};"></div></td>
                            <td class="code" style="padding:14px 28px 14px 22px;vertical-align:middle;{{ $font }}font-size:40px;font-weight:800;letter-spacing:12px;color:{{ $c['dark'] }};white-space:nowrap;">{{ $code }}</td>
                        </tr>
                    </table>
                </td></tr>

                {{-- Expiry note --}}
                <tr><td class="px" style="padding:0 36px 26px;">
                    <table role="presentation" cellpadding="0" cellspacing="0" border="0"><tr>
                        <td style="vertical-align:top;padding:2px 12px 0 0;"><img src="{{ $img('clock', 'clock.png') }}" width="24" height="24" alt="" style="display:block;border:0;"></td>
                        <td style="{{ $font }}font-size:15px;line-height:1.6;color:#475569;">
                            The code works for <strong style="color:{{ $c['brand'] }};">{{ $minutes }} minutes.</strong>
                            If you didn’t sign up for {{ $brand }}, you can ignore this email.
                        </td>
                    </tr></table>
                </td></tr>

                <tr><td class="px" style="padding:0 36px;"><div style="height:1px;background:{{ $c['line'] }};line-height:1px;font-size:0;">&nbsp;</div></td></tr>

                {{-- Sign-off --}}
                <tr><td class="px" style="padding:22px 36px 32px;{{ $font }}font-size:15px;line-height:1.7;color:#64748b;">
                    Happy Dining!<br>
                    <span style="color:#1c2430;">The <strong>{{ $brand }}</strong> Team</span>
                    <img src="{{ $img('heart', 'heart.png') }}" width="18" height="16" alt="" style="vertical-align:-2px;border:0;margin-left:4px;">
                </td></tr>
            </table>

        </td></tr>
    </table>
</body>
</html>
