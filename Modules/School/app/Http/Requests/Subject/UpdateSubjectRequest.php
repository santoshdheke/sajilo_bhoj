<?php

namespace Modules\School\Http\Requests\Subject;

use Illuminate\Validation\Rule;
use Modules\School\Enums\SubjectTypeEnum;
use Modules\School\Http\Requests\AdminFormRequest;

class UpdateSubjectRequest extends AdminFormRequest
{
    public function rules(): array
    {
        $id = $this->route('subject');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100', Rule::unique('school.subjects', 'name')->where('company_id', $this->schoolId())->ignore($id)],
            'code' => ['sometimes', 'nullable', 'string', 'max:20', Rule::unique('school.subjects', 'code')->where('company_id', $this->schoolId())->ignore($id)],
            'type' => ['sometimes', 'required', Rule::enum(SubjectTypeEnum::class)],
            'classroom_ids' => ['sometimes', 'nullable', 'array'],
            'classroom_ids.*' => ['integer', Rule::exists('school.classrooms', 'id')->where('company_id', $this->schoolId())],
        ];
    }

    /**
     * An unchecked checkbox list posts nothing; the form's marker tells "no classes" apart
     * from "not editing classes".
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('classroom_ids_submitted') && ! $this->has('classroom_ids')) {
            $this->merge(['classroom_ids' => []]);
        }
    }
}
