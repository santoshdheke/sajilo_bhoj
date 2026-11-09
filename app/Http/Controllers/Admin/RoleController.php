<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Support\Tenancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $role = Role::create($this->validated($request) + ['company_id' => Tenancy::company()?->id]);

        return response()->json($role->loadCount('users'));
    }

    public function update(Request $request, Role $role): JsonResponse
    {
        abort_if($role->isOwner(), 422, 'The Owner role always has full access and can’t be changed.');

        $role->update($this->validated($request, $role));

        return response()->json($role->loadCount('users'));
    }

    public function destroy(Role $role): JsonResponse
    {
        abort_if($role->isOwner(), 422, 'The Owner role can’t be deleted.');
        if ($count = $role->users()->count()) {
            return response()->json(['message' => "Move the {$count} staff member(s) in \"{$role->name}\" to another role first."], 422);
        }

        $role->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request, ?Role $role = null): array
    {
        $data = $request->validate([
            'name'          => ['required', 'string', 'max:60',
                                Rule::unique('roles', 'name')->where('company_id', Tenancy::company()?->id)->ignore($role?->id)],
            'description'   => 'nullable|string|max:255',
            'permissions'   => 'array',
            'permissions.*' => ['string', Rule::in(array_keys(Role::PERMISSIONS))],
        ], ['name.unique' => 'A role with this name already exists.']);

        $data['permissions'] = array_values(array_unique(array_merge(Role::ALWAYS, $data['permissions'] ?? [])));

        return $data;
    }
}
