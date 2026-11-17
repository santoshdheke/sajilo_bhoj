<?php

namespace Modules\School\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\School\Http\Requests\Classroom\StoreClassroomRequest;
use Modules\School\Http\Requests\Classroom\UpdateClassroomRequest;
use Modules\School\Services\Classroom\ClassroomService;
use Throwable;

class ClassroomController extends Controller
{
    public function __construct(private readonly ClassroomService $service) {}

    public function index()
    {
        try {
            return view('school::admin.classroom.index', ['classrooms' => $this->service->list()]);
        } catch (Throwable $e) {
            Log::error('Classroom index failed', ['error' => $e->getMessage()]);

            return redirect()->route('school.admin.dashboard')->with('error', 'Could not load classes.');
        }
    }

    public function create()
    {
        return view('school::admin.classroom.create', $this->formData());
    }

    public function store(StoreClassroomRequest $request)
    {
        try {
            $item = $this->service->create($request->validated(), $request->user());

            return redirect()->route('school.admin.classrooms.index')->with('success', "{$item->name} added.");
        } catch (Throwable $e) {
            Log::error('Classroom store failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not add the classroom.');
        }
    }

    public function edit(string $classroom)
    {
        try {
            return view('school::admin.classroom.edit', ['classroom' => $this->service->find((int) $classroom)] + $this->formData());
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Classroom edit failed', ['id' => $classroom, 'error' => $e->getMessage()]);

            return redirect()->route('school.admin.classrooms.index')->with('error', 'Could not load the classroom.');
        }
    }

    public function update(UpdateClassroomRequest $request, string $classroom)
    {
        try {
            $this->service->update($this->service->find((int) $classroom), $request->validated(), $request->user());

            return redirect()->route('school.admin.classrooms.index')->with('success', 'Classroom updated.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Classroom update failed', ['id' => $classroom, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not update the classroom.');
        }
    }

    public function destroy(string $classroom)
    {
        try {
            $this->service->delete($this->service->find((int) $classroom), request()->user());

            return redirect()->route('school.admin.classrooms.index')->with('success', 'Classroom deleted.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Classroom destroy failed', ['id' => $classroom, 'error' => $e->getMessage()]);

            return back()->with('error', 'Could not delete the classroom.');
        }
    }

    private function formData(): array
    {
        return [];
    }
}
