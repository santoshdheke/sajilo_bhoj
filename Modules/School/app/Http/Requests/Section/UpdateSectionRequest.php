<?php

namespace Modules\School\Http\Requests\Section;

use Illuminate\Validation\Rule;
use Modules\School\Http\Requests\AdminFormRequest;
use Modules\School\Models\Section;

class UpdateSectionRequest extends AdminFormRequest
{
    public function rules(): array
    {
        // A section stays in its class; moving it would invalidate its roll/assignment history.
        $classroomId = Section::query()->where('company_id', $this->schoolId())->whereKey($this->route('section'))->value('classroom_id');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:50',
                Rule::unique('school.sections', 'name')->where('classroom_id', $classroomId)->ignore($this->route('section'))],
            'capacity' => ['sometimes', 'nullable', 'integer', 'min:1', 'max:1000'],
            'class_teacher_id' => ['sometimes', 'nullable', 'integer', Rule::exists('school.users', 'id')->where('company_id', $this->schoolId())->where('role', 'teacher')],
        ];
    }
}
