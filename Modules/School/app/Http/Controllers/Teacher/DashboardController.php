<?php

namespace Modules\School\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Modules\School\Support\SchoolTenancy;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            return view('school::teacher.index', ['school' => SchoolTenancy::company()]);
        } catch (Throwable $e) {
            Log::error('Teacher dashboard failed', ['error' => $e->getMessage()]);

            return redirect('/')->with('error', 'Could not load the dashboard.');
        }
    }
}
