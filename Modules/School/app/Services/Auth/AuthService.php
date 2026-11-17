<?php

namespace Modules\School\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\User;
use Modules\School\Services\ActivityLog\ActivityLogService;
use Modules\School\Support\SchoolTenancy;
use RuntimeException;
use Throwable;

class AuthService
{
    public function __construct(private readonly ActivityLogService $activityLog) {}

    /**
     * Attempt a session login (guard "school") limited to this school's own users.
     * Returns the user on success, null on bad credentials (or an account of another school).
     */
    public function attempt(string $email, string $password, bool $remember = false): ?User
    {
        try {
            $schoolId = SchoolTenancy::id() ?? throw new RuntimeException('No school for this request.');

            $credentials = [
                'email' => $email,
                'password' => $password,
                'company_id' => $schoolId,
                'role' => UserRoleEnum::values(),
            ];

            $guard = Auth::guard('school');

            if (! $guard->attempt($credentials, $remember)) {
                $this->activityLog->log(null, 'auth.login_failed', properties: [
                    'email' => $email,
                ]);

                return null;
            }

            /** @var User $user */
            $user = $guard->user();

            $this->activityLog->log($user, 'auth.login', $user);

            return $user;
        } catch (Throwable $e) {
            Log::error('School AuthService::attempt failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function logout(User $user): void
    {
        try {
            Auth::guard('school')->logout();

            $this->activityLog->log($user, 'auth.logout', $user);
        } catch (Throwable $e) {
            Log::error('School AuthService::logout failed', ['id' => $user->getKey(), 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
