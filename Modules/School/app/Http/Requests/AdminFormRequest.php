<?php

namespace Modules\School\Http\Requests;

use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\User;
use Modules\School\Support\SchoolTenancy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Base for Admin form requests: school admins only, and a handle on the current school
 * so `exists`/`unique` rules can be tenant-scoped.
 */
abstract class AdminFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User && $user->hasRole(UserRoleEnum::Admin);
    }

    protected function schoolId(): int
    {
        return SchoolTenancy::id();
    }
}
