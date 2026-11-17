<?php

namespace Modules\School\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\School\Http\Requests\Auth\LoginRequest;
use Modules\School\Services\Auth\AuthService;
use Modules\School\Support\SchoolTenancy;
use Throwable;

/** The one /login + /logout of a school domain; each role lands on its own area. */
class LoginController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /** "/": the signed-in user's own area, else the login page. */
    public function home(): RedirectResponse
    {
        /** @var \Modules\School\Models\User|null $user */
        $user = Auth::guard('school')->user();

        return redirect()->route($user ? $user->homeRoute() : 'school.login');
    }

    public function create(): View|RedirectResponse
    {
        /** @var \Modules\School\Models\User|null $user */
        $user = Auth::guard('school')->user();

        if ($user) {
            return redirect()->route($user->homeRoute());
        }

        return view('school::auth.login', ['school' => SchoolTenancy::company()]);
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $user = $this->authService->attempt(
                $request->validated('email'),
                $request->validated('password'),
                $request->boolean('remember'),
            );

            if (! $user) {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors(['email' => 'These credentials do not match our records.']);
            }

            $request->session()->regenerate();

            // Back to the page that asked for login only if it's in this user's own area
            // (/admin, /teacher, /student): a teacher who first opened /admin/... shouldn't land on a 403.
            $intended = (string) $request->session()->pull('url.intended');
            $path = '/'.ltrim((string) parse_url($intended, PHP_URL_PATH), '/');

            return redirect(str_starts_with($path, '/'.$user->role->value.'/') ? $intended : route($user->homeRoute()));
        } catch (Throwable $e) {
            Log::error('School login failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Could not sign you in. Please try again.');
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            $this->authService->logout($request->user());

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('school.login');
        } catch (Throwable $e) {
            Log::error('School logout failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Could not sign you out. Please try again.');
        }
    }
}
