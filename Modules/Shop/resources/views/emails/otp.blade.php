<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your verification code</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;">Your code is {{ $code }}. It works for {{ $minutes }} minutes.</div>
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;padding:32px;">
                <tr><td style="font-size:20px;font-weight:bold;padding-bottom:8px;">Verify your email</td></tr>
                <tr><td style="font-size:15px;line-height:1.6;color:#4b5563;padding-bottom:20px;">
                    Enter this code on the SajiloBhoj shop sign-up page to confirm your email address.
                </td></tr>
                <tr><td align="center" style="padding-bottom:20px;">
                    <span style="display:inline-block;font-size:32px;letter-spacing:8px;font-weight:bold;background:#eaf2ff;color:#2563eb;border-radius:12px;padding:14px 22px;">{{ $code }}</span>
                </td></tr>
                <tr><td style="font-size:13px;color:#6b7280;line-height:1.6;">
                    The code works for {{ $minutes }} minutes. If you didn't try to create a shop, you can ignore this email.
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
