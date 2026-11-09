<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A restaurant owner must verify their email (6-digit code) before using the admin panel.
 * Staff accounts are created by the owner and are trusted, so they are not stopped here.
 */
class EnsureOwnerEmailVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('web');

        if ($user && ! $user->email_verified_at && $user->role?->isOwner()) {
            return $request->expectsJson()
                ? response()->json(['message' => 'Please verify your email first.'], 403)
                : redirect()->route('admin.verify_email.show');
        }

        return $next($request);
    }
}
