<?php

namespace Modules\School\Http\Requests\Section;

use Illuminate\Validation\Rule;
use Modules\School\Http\Requests\AdminFormRequest;

class AssignClassTeacherRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'class_teacher_id' => ['nullable', 'integer', Rule::exists('school.users', 'id')->where('company_id', $this->schoolId())->where('role', 'teacher')],
        ];
    }
}
