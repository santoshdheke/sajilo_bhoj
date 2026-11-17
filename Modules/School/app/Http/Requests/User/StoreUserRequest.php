<?php

namespace Modules\School\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\School\Enums\UserRoleEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class StoreUserRequest extends AdminFormRequest
{
    /**
     * No `company_id`: the new user always joins the school of the current domain.
     * Emails are unique per school (people sign in on their own school's domain).
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('school.users', 'email')->where('company_id', $this->schoolId())],
            'role' => ['required', Rule::enum(UserRoleEnum::class)],
            'password' => ['required', 'string', Password::min(8)],
        ];
    }
}
