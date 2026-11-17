<?php

namespace Modules\School\Services\Classroom;

use Modules\School\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\School\Models\ClassroomModel;
use Modules\School\Services\SchoolScopedService;
use Throwable;

class ClassroomService extends SchoolScopedService
{
    public function list(int $perPage = 15): LengthAwarePaginator
    {
        try {
            return $this->scoped()->withCount('sections')->orderBy('display_order')->orderBy('name')
                ->paginate($perPage)->withQueryString();
        } catch (Throwable $e) {
            Log::error('ClassroomService::list failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /** Every classroom of this school, for dropdowns. */
    public function all(): Collection
    {
        return $this->scoped()->orderBy('display_order')->orderBy('name')->get();
    }

    public function find(int $id): ClassroomModel
    {
        try {
            return $this->scoped()->findOrFail($id);
        } catch (Throwable $e) {
            Log::error('ClassroomService::find failed', ['classroom_id' => $id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function create(array $data, User $actor): ClassroomModel
    {
        try {
            return DB::connection('school')->transaction(function () use ($data, $actor) {
                $classroom = (new ClassroomModel)->forceFill($data + ['company_id' => $this->schoolId()]);
                $classroom->save();

                $this->activityLog->log($actor, 'classroom.created', $classroom, ['name' => $classroom->name]);

                return $classroom;
            });
        } catch (Throwable $e) {
            Log::error('ClassroomService::create failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function update(ClassroomModel $classroom, array $data, User $actor): ClassroomModel
    {
        try {
            return DB::connection('school')->transaction(function () use ($classroom, $data, $actor) {
                $classroom->update($data);

                $this->activityLog->log($actor, 'classroom.updated', $classroom, $data);

                return $classroom->refresh();
            });
        } catch (Throwable $e) {
            Log::error('ClassroomService::update failed', ['classroom_id' => $classroom->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function delete(ClassroomModel $classroom, User $actor): void
    {
        try {
            DB::connection('school')->transaction(function () use ($classroom, $actor) {
                $classroom->delete();

                $this->activityLog->log($actor, 'classroom.deleted', $classroom, ['name' => $classroom->name]);
            });
        } catch (Throwable $e) {
            Log::error('ClassroomService::delete failed', ['classroom_id' => $classroom->id, 'error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function scoped(): Builder
    {
        return ClassroomModel::query()->where('company_id', $this->schoolId());
    }
}
