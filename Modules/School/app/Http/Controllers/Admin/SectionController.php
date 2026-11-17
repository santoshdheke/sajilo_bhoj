<?php

namespace Modules\School\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Modules\School\Services\Classroom\ClassroomService;
use Modules\School\Http\Requests\Section\StoreSectionRequest;
use Modules\School\Http\Requests\Section\UpdateSectionRequest;
use Modules\School\Services\Section\SectionService;
use Throwable;

class SectionController extends Controller
{
    public function __construct(private readonly SectionService $service, private readonly ClassroomService $classrooms) {}

    public function index(Request $request)
    {
        try {
            $classroomId = $request->integer('classroom') ?: null;

            return view('school::admin.section.index', [
                'sections' => $this->service->list($classroomId),
                'classrooms' => $this->classrooms->all(),
                'classroomId' => $classroomId,
            ]);
        } catch (Throwable $e) {
            Log::error('Section index failed', ['error' => $e->getMessage()]);

            return redirect()->route('school.admin.dashboard')->with('error', 'Could not load sections.');
        }
    }

    public function create(Request $request)
    {
        return view('school::admin.section.create', $this->formData() + ['presetClassroom' => $request->integer('classroom') ?: null]);
    }

    public function store(StoreSectionRequest $request)
    {
        try {
            $item = $this->service->create($request->validated(), $request->user());

            return redirect()->route('school.admin.sections.index')->with('success', "{$item->name} added.");
        } catch (Throwable $e) {
            Log::error('Section store failed', ['error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not add the section.');
        }
    }

    public function edit(string $section)
    {
        try {
            return view('school::admin.section.edit', ['section' => $this->service->find((int) $section)] + $this->formData());
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Section edit failed', ['id' => $section, 'error' => $e->getMessage()]);

            return redirect()->route('school.admin.sections.index')->with('error', 'Could not load the section.');
        }
    }

    public function update(UpdateSectionRequest $request, string $section)
    {
        try {
            $this->service->update($this->service->find((int) $section), $request->validated(), $request->user());

            return redirect()->route('school.admin.sections.index')->with('success', 'Section updated.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Section update failed', ['id' => $section, 'error' => $e->getMessage()]);

            return back()->withInput()->with('error', 'Could not update the section.');
        }
    }

    public function destroy(string $section)
    {
        try {
            $this->service->delete($this->service->find((int) $section), request()->user());

            return redirect()->route('school.admin.sections.index')->with('success', 'Section deleted.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('Section destroy failed', ['id' => $section, 'error' => $e->getMessage()]);

            return back()->with('error', 'Could not delete the section.');
        }
    }

    private function formData(): array
    {
        return ['classrooms' => $this->classrooms->all(), 'teachers' => $this->service->teachers()];
    }
}
