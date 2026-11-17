<?php

namespace Modules\School\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Support\Workspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Modules\School\Http\Requests\Auth\RegisterSchoolRequest;
use Modules\School\Services\Registration\SchoolRegistrationService;
use Throwable;

/**
 * School sign-up on a host nobody owns yet (linked from the 410 "create your workspace" page).
 * The school's domain is the host being visited, never a form field.
 */
class RegisterSchoolController extends Controller
{
    public function __construct(private readonly SchoolRegistrationService $registration) {}

    public function create(Request $request): View|RedirectResponse
    {
        if (Workspace::company()) {
            return redirect('/');
        }

        return view('school::auth.register', ['domain' => $request->getHost()]);
    }

    public function store(RegisterSchoolRequest $request): RedirectResponse
    {
        $host = $request->getHost();

        // The domains table is shared by every module: one workspace per host.
        $existing = Domain::with('company')->where('domain', $host)->first();
        if ($existing?->company) {
            throw ValidationException::withMessages(['school_name' => "A workspace already exists for {$host}."]);
        }

        try {
            $existing?->delete(); // a domain row left without a company

            $admin = $this->registration->register($host, $request->validated());
            Workspace::flush();

            Auth::guard('school')->login($admin);
            $request->session()->regenerate();

            // A path, not a route name: only the sign-up routes are loaded until this host is a school domain.
            return redirect('/admin/dashboard')->with('success', 'Your school is ready.');
        } catch (Throwable $e) {
            Log::error('School registration failed', ['domain' => $host, 'error' => $e->getMessage()]);

            return back()->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Could not register the school. Please try again.');
        }
    }
}
