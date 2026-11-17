<?php

namespace Modules\School\Http\Requests\Classroom;

use Illuminate\Validation\Rule;
use Modules\School\Enums\ClassroomStatusEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class StoreClassroomRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('school.classrooms', 'name')->where('company_id', $this->schoolId())],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:65000'],
            'status' => ['required', Rule::enum(ClassroomStatusEnum::class)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['display_order' => $this->input('display_order') ?: 0]);
    }
}
