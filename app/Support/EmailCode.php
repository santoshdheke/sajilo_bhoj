<?php

namespace App\Support;

use App\Mail\VerificationCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * 6-digit email verification codes for restaurant owners.
 * Only a hash of the code is kept (in the cache), it expires after 15 minutes,
 * allows 5 wrong tries, and a new one can be sent once a minute.
 */
class EmailCode
{
    public const TTL_MINUTES  = 15;
    public const MAX_ATTEMPTS = 5;
    public const RESEND_AFTER = 60; // seconds

    public static function send(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        Cache::put(self::key($user), ['hash' => Hash::make($code), 'email' => $user->email, 'attempts' => 0],
            now()->addMinutes(self::TTL_MINUTES));
        Cache::put(self::key($user, 'sent'), now()->timestamp, self::RESEND_AFTER);

        Mail::to($user->email)->send(new VerificationCodeMail($user, $code));
    }

    public static function hasActiveCode(User $user): bool
    {
        return ($entry = Cache::get(self::key($user))) && $entry['email'] === $user->email;
    }

    /** Seconds until another code may be sent (0 = now). */
    public static function resendIn(User $user): int
    {
        $sent = Cache::get(self::key($user, 'sent'));

        return $sent ? max(0, self::RESEND_AFTER - (now()->timestamp - $sent)) : 0;
    }

    /** Returns null when the code is right, otherwise a message for the user. */
    public static function check(User $user, string $code): ?string
    {
        $entry = Cache::get(self::key($user));

        if (! $entry || $entry['email'] !== $user->email) {
            return 'This code has expired. Please send a new one.';
        }
        if ($entry['attempts'] >= self::MAX_ATTEMPTS) {
            return 'Too many wrong tries. Please send a new code.';
        }
        if (! Hash::check($code, $entry['hash'])) {
            $entry['attempts']++;
            Cache::put(self::key($user), $entry, now()->addMinutes(self::TTL_MINUTES));
            $left = self::MAX_ATTEMPTS - $entry['attempts'];

            return $left > 0 ? "That code isn't right. {$left} " . ($left === 1 ? 'try' : 'tries') . ' left.'
                             : 'Too many wrong tries. Please send a new code.';
        }

        Cache::forget(self::key($user));

        return null;
    }

    private static function key(User $user, string $suffix = 'code'): string
    {
        return "email-verify:{$suffix}:{$user->id}";
    }
}
