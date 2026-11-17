<?php

namespace Modules\School\Http\Requests\Section;

use Illuminate\Validation\Rule;
use Modules\School\Http\Requests\AdminFormRequest;

class StoreSectionRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'classroom_id' => ['required', 'integer', Rule::exists('school.classrooms', 'id')->where('company_id', $this->schoolId())],
            'name' => ['required', 'string', 'max:50', Rule::unique('school.sections', 'name')->where('classroom_id', $this->input('classroom_id'))],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:1000'],
            'class_teacher_id' => ['nullable', 'integer', Rule::exists('school.users', 'id')->where('company_id', $this->schoolId())->where('role', 'teacher')],
        ];
    }
}
