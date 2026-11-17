<?php

namespace Modules\School\Services\Subject;

use Modules\School\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\School\Models\ClassroomModel;
use Modules\School\Models\Subject;
use Modules\School\Services\SchoolScopedService;
use Throwable;

class SubjectService extends SchoolScopedService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->scoped()->with('classrooms')->orderBy('name')->paginate($perPage)->withQueryString();
        } catch (Throwable $e) {
            Log::error('SubjectService::list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function find(int $id): Subject
    {
        try {
            return $this->scoped()->with('classrooms')->findOrFail($id);
        } catch (Throwable $e) {
            Log::error('SubjectService::find failed', ['subject_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * @param  array{classroom_ids?: array<int>}  $data
     */
    public function create(array $data, User $actor): Subject
    {
        try {
            return DB::connection('school')->transaction(function () use ($data, $actor) {
                $classroomIds = $this->ownClassroomIds($data['classroom_ids'] ?? []);
                unset($data['classroom_ids']);

                $subject = (new Subject)->forceFill($data + ['company_id' => $this->schoolId()]);
                $subject->save();
                $subject->classrooms()->sync($classroomIds);

                $this->activityLog->log($actor, 'subject.created', $subject, ['name' => $subject->name, 'classroom_ids' => $classroomIds]);

                return $subject;
            });
        } catch (Throwable $e) {
            Log::error('SubjectService::create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(Subject $subject, array $data, User $actor): Subject
    {
        try {
            return DB::connection('school')->transaction(function () use ($subject, $data, $actor) {
                $classroomIds = array_key_exists('classroom_ids', $data) ? $this->ownClassroomIds($data['classroom_ids'] ?? []) : null;
                unset($data['classroom_ids']);

                $subject->update($data);
                if ($classroomIds !== null) {
                    $subject->classrooms()->sync($classroomIds);
                }

                $this->activityLog->log($actor, 'subject.updated', $subject, $data + ['classroom_ids' => $classroomIds]);

                return $subject->refresh();
            });
        } catch (Throwable $e) {
            Log::error('SubjectService::update failed', ['subject_id' => $subject->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(Subject $subject, User $actor): void
    {
        try {
            DB::connection('school')->transaction(function () use ($subject, $actor) {
                $subject->delete();

                $this->activityLog->log($actor, 'subject.deleted', $subject, ['name' => $subject->name]);
            });
        } catch (Throwable $e) {
            Log::error('SubjectService::delete failed', ['subject_id' => $subject->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    /** Drops any id that isn't a classroom of this school (never attach across schools). */
    private function ownClassroomIds(array $ids): array
    {
        return ClassroomModel::query()->where('company_id', $this->schoolId())
            ->whereIn('id', $ids)->pluck('id')->all();
    }

    private function scoped(): Builder
    {
        return Subject::query()->where('company_id', $this->schoolId());
    }
}
