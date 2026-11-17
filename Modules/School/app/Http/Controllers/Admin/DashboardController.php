<?php

namespace Modules\School\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\School\Services\Dashboard\DashboardService;
use Throwable;

class DashboardController extends Controller
{
    public function __construct(private readonly DashboardService $dashboard) {}

    public function index(): View|RedirectResponse
    {
        try {
            return view('school::admin.dashboard.index', $this->dashboard->summary());
        } catch (Throwable $e) {
            Log::error('Admin dashboard failed', ['error' => $e->getMessage()]);

            return redirect('/')->with('error', 'Could not load the dashboard.');
        }
    }
}
