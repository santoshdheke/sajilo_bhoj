<?php

namespace Modules\School\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Modules\School\Enums\UserRoleEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class UpdateUserRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $userId = (int) $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255',
                Rule::unique('school.users', 'email')->where('company_id', $this->schoolId())->ignore($userId)],
            'role' => ['sometimes', 'required', Rule::enum(UserRoleEnum::class)],
            'password' => ['nullable', 'string', Password::min(8)],
        ];
    }
}
