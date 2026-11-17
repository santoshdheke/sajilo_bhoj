<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You're invited</title>
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Arial,Helvetica,sans-serif;color:#1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 12px;">
        <tr><td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background:#ffffff;border-radius:16px;padding:32px;">
                <tr><td style="font-size:20px;font-weight:bold;padding-bottom:8px;">Hi {{ $staffName }},</td></tr>
                <tr><td style="font-size:15px;line-height:1.6;color:#4b5563;padding-bottom:24px;">
                    You've been added to the staff of <strong>{{ $shopName }}</strong> on SajiloBhoj.
                    Choose your password to activate your account.
                </td></tr>
                <tr><td align="center" style="padding-bottom:24px;">
                    <a href="{{ $link }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:bold;border-radius:10px;padding:12px 26px;">Set my password</a>
                </td></tr>
                <tr><td style="font-size:12px;color:#6b7280;line-height:1.6;word-break:break-all;">
                    Or open this link: {{ $link }}
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
