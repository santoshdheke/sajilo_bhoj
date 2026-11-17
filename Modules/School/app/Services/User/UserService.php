<?php

namespace Modules\School\Services\User;

use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\User;
use Modules\School\Services\ActivityLog\ActivityLogService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\School\Exceptions\CannotDeleteOwnAccountException;
use Modules\School\Support\SchoolTenancy;
use RuntimeException;
use Throwable;

/**
 * A school admin's view of the `users` table: every query is scoped to the school of
 * the current domain, and new users always land in that school. The school is never
 * read from the request payload.
 */
class UserService
{
    public function __construct(
        private readonly ActivityLogService $activityLog,
    ) {}

    /**
     * @param  string|null  $role  Restrict to one role (an invalid value is ignored, not an error).
     */
    public function list(int $perPage = 15, ?string $role = null): LengthAwarePaginator
    {
        try {
            $query = $this->scoped();

            if ($role && in_array($role, UserRoleEnum::values(), true)) {
                $query->where('role', $role);
            }

            return $query->orderBy('name')->paginate($perPage)->withQueryString();
        } catch (Throwable $e) {
            Log::error('UserService::list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @throws ModelNotFoundException when the user isn't in this school
     */
    public function find(int $id): User
    {
        try {
            return $this->scoped()->findOrFail($id);
        } catch (Throwable $e) {
            Log::error('UserService::find failed', ['user_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data, User $actor): User
    {
        try {
            return DB::connection('school')->transaction(function () use ($data, $actor) {
                $user = User::create($data + ['company_id' => $this->schoolId()]);

                $this->activityLog->log($actor, 'user.created', $user, [
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                ]);

                return $user;
            });
        } catch (Throwable $e) {
            Log::error('UserService::create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(User $user, array $data, User $actor): User
    {
        try {
            return DB::connection('school')->transaction(function () use ($user, $data, $actor) {
                // An empty password means "leave it alone".
                if (empty($data['password'])) {
                    unset($data['password']);
                }

                // Nobody can demote themselves out of admin and lock the school out.
                if ($user->is($actor)) {
                    unset($data['role']);
                }

                $user->update($data);

                $this->activityLog->log($actor, 'user.updated', $user, array_diff_key($data, ['password' => true]));

                return $user->refresh();
            });
        } catch (Throwable $e) {
            Log::error('UserService::update failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(User $user, User $actor): void
    {
        if ($user->is($actor)) {
            throw new CannotDeleteOwnAccountException;
        }

        try {
            DB::connection('school')->transaction(function () use ($user, $actor) {
                $user->delete();

                $this->activityLog->log($actor, 'user.deleted', $user, ['email' => $user->email]);
            });
        } catch (Throwable $e) {
            Log::error('UserService::delete failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function scoped(): Builder
    {
        return User::query()->where('company_id', $this->schoolId());
    }

    private function schoolId(): int
    {
        $school = SchoolTenancy::company();

        if (! $school) {
            throw new RuntimeException('No active school for this request.');
        }

        return $school->id;
    }
}
