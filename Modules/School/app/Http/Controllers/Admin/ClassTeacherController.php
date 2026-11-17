<?php

namespace Modules\School\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\School\Http\Requests\Section\AssignClassTeacherRequest;
use Modules\School\Services\Section\SectionService;
use Throwable;

/**
 * "Class Teachers": one page listing every section with a picker for its class teacher.
 */
class ClassTeacherController extends Controller
{
    public function __construct(private readonly SectionService $sections) {}

    public function index()
    {
        try {
            return view('school::admin.class-teacher.index', [
                'sections' => $this->sections->list(perPage: 50),
                'teachers' => $this->sections->teachers(),
            ]);
        } catch (Throwable $e) {
            Log::error('ClassTeacher index failed', ['error' => $e->getMessage()]);

            return redirect()->route('school.admin.dashboard')->with('error', 'Could not load class teachers.');
        }
    }

    public function update(AssignClassTeacherRequest $request, string $section)
    {
        try {
            $this->sections->assignClassTeacher(
                $this->sections->find((int) $section),
                $request->validated('class_teacher_id'),
                $request->user(),
            );

            return back()->with('success', 'Class teacher updated.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('ClassTeacher update failed', ['section_id' => $section, 'error' => $e->getMessage()]);

            return back()->with('error', 'Could not update the class teacher.');
        }
    }
}
