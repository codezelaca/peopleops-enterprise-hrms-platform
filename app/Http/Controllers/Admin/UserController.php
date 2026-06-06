<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function store(StoreUserRequest $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();
        $validated = $request->validated();
        $password = $validated['password'];

        DB::transaction(function () use ($admin, $validated): void {
            /** @var User $user */
            $user = User::query()->create([
                ...Arr::only($validated, ['name', 'email', 'nic', 'phone', 'job_title', 'status', 'password']),
                'company_id' => $admin->company_id,
                'email_verified_at' => now(),
            ]);

            $user->syncRoles($validated['roles'] ?? []);

            activity()
                ->performedOn($user)
                ->causedBy($admin)
                ->event('created')
                ->withProperties(['roles' => $validated['roles'] ?? []])
                ->log('User account created by administrator');
        });

        return back()
            ->with('success', 'User account created.')
            ->with('createdUserCredentials', [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $password,
            ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();
        $this->ensureCompanyUser($admin, $user);

        if ($user->hasRole(SystemRoles::ADMIN)) {
            abort(403, 'The protected administrator account cannot be managed here.');
        }

        $validated = $request->validated();

        if ($admin->is($user) && $validated['status'] !== 'active') {
            return back()->with('error', 'You cannot suspend your own administrator session.');
        }

        DB::transaction(function () use ($admin, $user, $validated): void {
            $auditedFields = ['name', 'email', 'nic', 'phone', 'job_title', 'status'];
            $oldAttributes = $user->only($auditedFields);
            $oldRoles = $user->roles()->pluck('name')->sort()->values()->all();
            $data = Arr::only($validated, ['name', 'email', 'nic', 'phone', 'job_title', 'status']);

            if (filled($validated['password'] ?? null)) {
                $data['password'] = $validated['password'];
            }

            $user->update($data);
            $user->syncRoles($validated['roles'] ?? []);
            $user->refresh();

            $newAttributes = $user->only($auditedFields);
            $newRoles = $user->roles()->pluck('name')->sort()->values()->all();
            $changedOld = [];
            $changedNew = [];

            foreach ($auditedFields as $field) {
                if (($oldAttributes[$field] ?? null) !== ($newAttributes[$field] ?? null)) {
                    $changedOld[$field] = $oldAttributes[$field] ?? null;
                    $changedNew[$field] = $newAttributes[$field] ?? null;
                }
            }

            $properties = [
                'old' => $changedOld,
                'attributes' => $changedNew,
            ];

            if ($oldRoles !== $newRoles) {
                $properties['roles'] = $newRoles;
            }

            activity()
                ->performedOn($user)
                ->causedBy($admin)
                ->event('updated')
                ->withProperties($properties)
                ->log('User account updated by administrator');
        });

        return back()->with('success', 'User account updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        /** @var User $admin */
        $admin = auth()->user();
        $this->ensureCompanyUser($admin, $user);

        if ($admin->is($user)) {
            return back()->with('error', 'You cannot delete your own administrator account.');
        }

        if ($user->hasRole(SystemRoles::ADMIN)) {
            abort(403, 'The protected administrator account cannot be deleted.');
        }

        DB::transaction(function () use ($admin, $user): void {
            $user->syncRoles([]);
            $user->delete();

            activity()
                ->performedOn($user)
                ->causedBy($admin)
                ->event('deleted')
                ->log('User account deleted by administrator');
        });

        return back()->with('success', 'User account deleted.');
    }

    private function ensureCompanyUser(User $admin, User $user): void
    {
        abort_unless($admin->company_id !== null && $user->company_id === $admin->company_id, 404);
    }
}
