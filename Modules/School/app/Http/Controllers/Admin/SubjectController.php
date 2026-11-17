<?php

namespace Modules\School\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\School\Services\Classroom\ClassroomService;
use Modules\School\Http\Requests\Subject\StoreSubjectRequest;
use Modules\School\Http\Requests\Subject\UpdateSubjectRequest;
use Modules\School\Services\Subject\SubjectService;
use Throwable;

class SubjectController extends Controller
{
    public function __construct(private readonly SubjectService $service, private readonly ClassroomService $classrooms) {}

    public function index()
    {
        try {
            return view('school::admin.subject.index', ['subjects' => $this->service->list()]);
        } catch (Throwable $e) {
            Log::error('Subject index failed', ['error' => $e->getMessage()]);

            return redirect()->route('school.admin.dashboard')->with('error', 'Could not load subjects.');
        }
    }

    public function create()
    {
        return view('school::admin.subject.create', $this->formData());
    }

    public function store(StoreSubjectRequest $request)
    {
        try {
            $item = $this->service->create($request->validated(), $request->user());

            return redirect()->route('school.admin.subjects.index')->with('success', "{$item->name} added.");
        } catch (Throwable $e) {
            Log::error('Subject store failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not add the subject.');
        }
    }

    public function edit(string $subject)
    {
        try {
            return view('school::admin.subject.edit', ['subject' => $this->service->find((int) $subject)] + $this->formData());
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Subject edit failed', ['id' => $subject, 'error' => $e->getMessage()]);

            return redirect()->route('school.admin.subjects.index')->with('error', 'Could not load the subject.');
        }
    }

    public function update(UpdateSubjectRequest $request, string $subject)
    {
        try {
            $this->service->update($this->service->find((int) $subject), $request->validated(), $request->user());

            return redirect()->route('school.admin.subjects.index')->with('success', 'Subject updated.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Subject update failed', ['id' => $subject, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not update the subject.');
        }
    }

    public function destroy(string $subject)
    {
        try {
            $this->service->delete($this->service->find((int) $subject), request()->user());

            return redirect()->route('school.admin.subjects.index')->with('success', 'Subject deleted.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Subject destroy failed', ['id' => $subject, 'error' => $e->getMessage()]);

            return back()->with('error', 'Could not delete the subject.');
        }
    }

    private function formData(): array
    {
        return ['classrooms' => $this->classrooms->all()];
    }
}
