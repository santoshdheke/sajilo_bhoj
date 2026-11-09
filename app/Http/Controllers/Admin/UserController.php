<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\DefaultRoles;
use App\Support\Tenancy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Staff accounts of the current restaurant only. Users have no automatic company scope,
 * so every query and every {user} from the URL is checked against the current company here.
 */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $company = Tenancy::company() ?? abort(404);
        DefaultRoles::ensure($company);

        $users = $this->staff()->with('role')->orderBy('name')->get();
        $roles = Role::withCount('users')->orderByRaw("JSON_CONTAINS(permissions, '\"*\"') DESC")->orderBy('name')->get();
        $tab   = $request->query('tab') === 'roles' ? 'roles' : 'staff';
        $permissionLabels = Role::PERMISSIONS;

        return view('admin.theme_one.user.index', compact('users', 'roles', 'tab', 'permissionLabels'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users,email',
            'role_id'  => ['required', $this->roleRule()],
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', Password::min(8)],
        ]);

        $user = User::create([
            'company_id' => Tenancy::company()->id,
            'branch_id'  => Tenancy::branch()?->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role_id'    => $data['role_id'],
            'phone'      => $data['phone'] ?? null,
            'password'   => Hash::make($data['password']),
            'is_active'  => true,
        ]);
        // Created by the owner, so trusted: only owners who sign up verify by email.
        $user->forceFill(['email_verified_at' => now()])->save();

        return response()->json($this->payload($user));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $this->ownCompany($user);

        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|max:255|unique:users,email,' . $user->id,
            'role_id'  => ['required', $this->roleRule()],
            'phone'    => 'nullable|string|max:20',
            'password' => ['nullable', Password::min(8)],
        ]);

        if ((int) $data['role_id'] !== (int) $user->role_id) {
            abort_if($user->is($request->user()), 422, 'You can’t change your own role.');
            $this->keepAnOwner($user);
        }

        $user->fill(collect($data)->except('password')->all());
        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        return response()->json($this->payload($user));
    }

    public function toggleActive(Request $request, User $user): JsonResponse
    {
        $this->ownCompany($user);
        abort_if($user->is($request->user()), 422, 'You can’t deactivate your own account.');
        if ($user->is_active) {
            $this->keepAnOwner($user);
        }

        $user->update(['is_active' => ! $user->is_active]);

        return response()->json(['is_active' => $user->is_active]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->ownCompany($user);
        abort_if($user->is($request->user()), 422, 'You can’t delete your own account.');
        $this->keepAnOwner($user);

        $user->delete();

        return response()->json(['success' => true]);
    }

    private function staff(): Builder
    {
        return User::where('company_id', Tenancy::company()?->id);
    }

    private function ownCompany(User $user): void
    {
        abort_unless(Tenancy::company() && $user->company_id === Tenancy::company()->id, 404);
    }

    /** Only roles of this restaurant can be assigned. */
    private function roleRule()
    {
        return Rule::exists('roles', 'id')->where('company_id', Tenancy::company()?->id);
    }

    /** Never remove, deactivate or demote the last active owner — someone must keep full access. */
    private function keepAnOwner(User $user): void
    {
        if (! $user->role?->isOwner() || ! $user->is_active) {
            return;
        }
        $owners = $this->staff()->where('is_active', true)->whereIn('role_id', Role::all()->filter->isOwner()->pluck('id'))->count();
        abort_if($owners <= 1, 422, 'This is the only active Owner. Make someone else an Owner first.');
    }

    private function payload(User $user): array
    {
        $user->load('role');

        return [
            'id'        => $user->id,
            'name'      => $user->name,
            'email'     => $user->email,
            'phone'     => $user->phone,
            'role_id'   => $user->role_id,
            'role_name' => $user->role?->name,
            'is_active' => $user->is_active,
            'joined'    => $user->created_at?->format('d M Y'),
        ];
    }
}
