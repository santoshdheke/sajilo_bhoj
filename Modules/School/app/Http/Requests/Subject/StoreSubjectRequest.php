<?php

namespace Modules\School\Http\Requests\Subject;

use Illuminate\Validation\Rule;
use Modules\School\Enums\SubjectTypeEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class StoreSubjectRequest extends AdminFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('school.subjects', 'name')->where('company_id', $this->schoolId())],
            'code' => ['nullable', 'string', 'max:20', Rule::unique('school.subjects', 'code')->where('company_id', $this->schoolId())],
            'type' => ['required', Rule::enum(SubjectTypeEnum::class)],
            'classroom_ids' => ['nullable', 'array'],
            'classroom_ids.*' => ['integer', Rule::exists('school.classrooms', 'id')->where('company_id', $this->schoolId())],
        ];
    }
}
