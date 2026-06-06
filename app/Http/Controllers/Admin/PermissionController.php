<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use App\Support\SystemPermissions;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function store(StorePermissionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $permission = Permission::query()->create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        activity()
            ->performedOn($permission)
            ->causedBy($request->user())
            ->event('created')
            ->log('Permission created by administrator');

        return back()->with('success', 'Permission created.');
    }

    public function update(UpdatePermissionRequest $request, Permission $permission): RedirectResponse
    {
        abort_if(
            in_array($permission->name, SystemPermissions::all(), true),
            403,
            'System permissions are protected.'
        );

        $permission->update($request->validated());

        activity()
            ->performedOn($permission)
            ->causedBy($request->user())
            ->event('updated')
            ->log('Permission updated by administrator');

        return back()->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        abort_if(
            in_array($permission->name, SystemPermissions::all(), true),
            403,
            'System permissions are protected.'
        );

        if ($permission->roles()->exists()) {
            return back()->with('error', 'This permission is assigned to roles and cannot be deleted.');
        }

        $permission->delete();

        return back()->with('success', 'Permission deleted.');
    }
}
