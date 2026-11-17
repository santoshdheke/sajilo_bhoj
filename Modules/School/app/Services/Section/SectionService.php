<?php

namespace Modules\School\Services\Section;

use Modules\School\Enums\UserRoleEnum;
use Modules\School\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\School\Models\Section;
use Modules\School\Services\SchoolScopedService;
use Throwable;

class SectionService extends SchoolScopedService
{
    public function list(?int $classroomId = null, int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->scoped()->with(['classroom', 'classTeacher'])
                ->when($classroomId, fn (Builder $q) => $q->where('classroom_id', $classroomId))
                ->orderBy('classroom_id')->orderBy('name')
                ->paginate($perPage)->withQueryString();
        } catch (Throwable $e) {
            Log::error('SectionService::list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function find(int $id): Section
    {
        try {
            return $this->scoped()->findOrFail($id);
        } catch (Throwable $e) {
            Log::error('SectionService::find failed', ['section_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /** Teachers of this school who can be made a class teacher. */
    public function teachers(): Collection
    {
        return User::query()->where('company_id', $this->schoolId())
            ->where('role', UserRoleEnum::Teacher->value)->orderBy('name')->get();
    }

    public function create(array $data, User $actor): Section
    {
        try {
            return DB::connection('school')->transaction(function () use ($data, $actor) {
                $section = (new Section)->forceFill($data + ['company_id' => $this->schoolId()]);
                $section->save();

                $this->activityLog->log($actor, 'section.created', $section, ['name' => $section->name, 'classroom_id' => $section->classroom_id]);

                return $section;
            });
        } catch (Throwable $e) {
            Log::error('SectionService::create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(Section $section, array $data, User $actor): Section
    {
        try {
            return DB::connection('school')->transaction(function () use ($section, $data, $actor) {
                $section->update($data);

                $this->activityLog->log($actor, 'section.updated', $section, $data);

                return $section->refresh();
            });
        } catch (Throwable $e) {
            Log::error('SectionService::update failed', ['section_id' => $section->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function assignClassTeacher(Section $section, ?int $teacherId, User $actor): Section
    {
        try {
            return DB::connection('school')->transaction(function () use ($section, $teacherId, $actor) {
                $section->update(['class_teacher_id' => $teacherId]);

                $this->activityLog->log($actor, 'section.class_teacher_assigned', $section, ['class_teacher_id' => $teacherId]);

                return $section->refresh();
            });
        } catch (Throwable $e) {
            Log::error('SectionService::assignClassTeacher failed', ['section_id' => $section->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(Section $section, User $actor): void
    {
        try {
            DB::connection('school')->transaction(function () use ($section, $actor) {
                $section->delete();

                $this->activityLog->log($actor, 'section.deleted', $section, ['name' => $section->name]);
            });
        } catch (Throwable $e) {
            Log::error('SectionService::delete failed', ['section_id' => $section->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function scoped(): Builder
    {
        return Section::query()->where('company_id', $this->schoolId());
    }
}
