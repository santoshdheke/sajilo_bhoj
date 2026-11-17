<?php

namespace Modules\School\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\School\Enums\UserRoleEnum;

/**
 * A school person — admin, teacher or student — always of one school (company_id).
 * Guard "school". (sms's App\Models\User.)
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $connection = 'school';

    protected $fillable = ['name', 'email', 'password', 'role', 'company_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRoleEnum::class,
        ];
    }

    /**
     * The school this user belongs to.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Route name of the landing page after login (depends on role).
     */
    public function homeRoute(): string
    {
        return $this->role->homeRoute();
    }

    public function hasRole(UserRoleEnum|string ...$roles): bool
    {
        $roles = array_map(
            fn (UserRoleEnum|string $role) => $role instanceof UserRoleEnum ? $role->value : $role,
            $roles
        );

        return in_array($this->role->value, $roles, true);
    }
}
