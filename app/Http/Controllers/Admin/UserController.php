<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get(['id', 'name']);

        return view('admin.theme_one.user.index', compact('users', 'roles'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email',
            'role_id'  => 'required|exists:roles,id',
            'phone'    => 'nullable|string|max:20',
            'password' => ['required', Password::min(8)],
        ]);

        $company = Tenancy::company();
        $branch  = Tenancy::branch();

        $user = User::create([
            'company_id' => $company?->id,
            'branch_id'  => $branch?->id,
            'name'       => $data['name'],
            'email'      => $data['email'],
            'role_id'    => $data['role_id'],
            'phone'      => $data['phone'] ?? null,
            'password'   => Hash::make($data['password']),
            'is_active'  => true,
        ]);

        return response()->json($user->load('role'));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'phone'   => 'nullable|string|max:20',
        ]);

        $user->update($data);

        return response()->json($user->load('role'));
    }

    public function toggleActive(User $user): JsonResponse
    {
        $user->update(['is_active' => !$user->is_active]);
        return response()->json(['is_active' => $user->is_active]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return response()->json(['success' => true]);
    }
}
