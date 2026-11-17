<?php

namespace Modules\School\Http\Controllers\Admin;

use Modules\School\Enums\UserRoleEnum;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Modules\School\Exceptions\CannotDeleteOwnAccountException;
use Modules\School\Http\Requests\User\StoreUserRequest;
use Modules\School\Http\Requests\User\UpdateUserRequest;
use Modules\School\Services\User\UserService;
use Throwable;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(Request $request)
    {
        try {
            $role = $request->query('role');
            $users = $this->userService->list(role: $role);
            $roleLabel = $role ? UserRoleEnum::tryFrom($role)?->label() : null;

            return view('school::admin.user.index', compact('users', 'role', 'roleLabel'));
        } catch (Throwable $e) {
            Log::error('User index failed', ['error' => $e->getMessage()]);

            return redirect('/')->with('error', 'Could not load users.');
        }
    }

    public function create(Request $request): View
    {
        return view('school::admin.user.create', ['presetRole' => $request->query('role')]);
    }

    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->userService->create($request->validated(), $request->user());

            return redirect()
                ->route('school.admin.users.index')
                ->with('success', "{$user->name} added.");
        } catch (Throwable $e) {
            Log::error('User store failed', ['error' => $e->getMessage()]);

            return back()->withInput($request->except('password'))->with('error', 'Could not add the user.');
        }
    }

    public function edit(string $user)
    {
        try {
            return view('school::admin.user.edit', ['user' => $this->userService->find((int) $user)]);
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('User edit failed', ['user_id' => $user, 'error' => $e->getMessage()]);

            return redirect()->route('school.admin.users.index')->with('error', 'Could not load the user.');
        }
    }

    public function update(UpdateUserRequest $request, string $user)
    {
        try {
            $this->userService->update($this->userService->find((int) $user), $request->validated(), $request->user());

            return redirect()
                ->route('school.admin.users.index')
                ->with('success', 'User updated.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (Throwable $e) {
            Log::error('User update failed', ['user_id' => $user, 'error' => $e->getMessage()]);

            return back()->withInput($request->except('password'))->with('error', 'Could not update the user.');
        }
    }

    public function destroy(string $user)
    {
        try {
            $this->userService->delete($this->userService->find((int) $user), request()->user());

            return redirect()
                ->route('school.admin.users.index')
                ->with('success', 'User deleted.');
        } catch (ModelNotFoundException) {
            abort(404);
        } catch (CannotDeleteOwnAccountException $e) {
            return back()->with('error', $e->getMessage());
        } catch (Throwable $e) {
            Log::error('User destroy failed', ['user_id' => $user, 'error' => $e->getMessage()]);

            return back()->with('error', 'Could not delete the user.');
        }
    }
}
