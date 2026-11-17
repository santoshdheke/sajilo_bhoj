<?php

namespace Modules\School\Http\Requests\Classroom;

use Illuminate\Validation\Rule;
use Modules\School\Enums\ClassroomStatusEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class UpdateClassroomRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:100',
                Rule::unique('school.classrooms', 'name')->where('company_id', $this->schoolId())->ignore($this->route('classroom'))],
            'display_order' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:65000'],
            'status' => ['sometimes', 'required', Rule::enum(ClassroomStatusEnum::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('display_order')) {
            $this->merge(['display_order' => $this->input('display_order') ?: 0]);
        }
    }
}
