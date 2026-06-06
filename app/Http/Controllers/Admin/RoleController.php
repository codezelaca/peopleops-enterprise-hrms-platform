<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Requests\Admin\UpdateRoleRequest;
use App\Support\SystemRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($request, $validated): void {
            $role = Role::query()->create([
                'name' => $validated['name'],
                'guard_name' => 'web',
            ]);

            $role->syncPermissions($validated['permissions'] ?? []);

            activity()
                ->performedOn($role)
                ->causedBy($request->user())
                ->event('created')
                ->withProperties(['permissions' => $validated['permissions'] ?? []])
                ->log('Role created by administrator');
        });

        return back()->with('success', 'Role created.');
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        abort_if($role->name === SystemRoles::ADMIN, 403, 'The administrator role is protected.');

        $validated = $request->validated();

        DB::transaction(function () use ($request, $role, $validated): void {
            $role->update(['name' => $validated['name']]);
            $role->syncPermissions($validated['permissions'] ?? []);

            activity()
                ->performedOn($role)
                ->causedBy($request->user())
                ->event('updated')
                ->withProperties(['permissions' => $validated['permissions'] ?? []])
                ->log('Role updated by administrator');
        });

        return back()->with('success', 'Role updated.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if($role->name === SystemRoles::ADMIN, 403, 'The administrator role is protected.');

        if ($role->users()->exists()) {
            return back()->with('error', 'This role is assigned to users and cannot be deleted.');
        }

        DB::transaction(function () use ($role): void {
            $role->syncPermissions([]);
            $role->delete();
        });

        return back()->with('success', 'Role deleted.');
    }
}
