<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\SystemPermissions;
use App\Support\SystemRoles;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AccessController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $filters = [
            'user_search' => $request->string('user_search')->trim()->value(),
            'status' => $request->string('status')->trim()->value(),
            'role' => $request->string('role')->trim()->value(),
            'role_search' => $request->string('role_search')->trim()->value(),
            'permission_search' => $request->string('permission_search')->trim()->value(),
            'tab' => $request->string('tab')->trim()->value() ?: 'users',
        ];

        return Inertia::render('admin/users/Index', [
            'users' => $this->users($request, $filters),
            'roles' => $this->roles($request, $filters),
            'permissions' => $this->permissions($request, $filters),
            'assignableRoles' => $this->assignableRoles(),
            'allPermissions' => $this->allPermissions(),
            'filters' => $filters,
            'stats' => $this->stats($request),
            'protected' => [
                'roles' => [SystemRoles::ADMIN],
                'permissions' => SystemPermissions::all(),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    private function users(Request $request, array $filters): array
    {
        /** @var User $admin */
        $admin = $request->user();

        $users = User::query()
            ->with('roles:id,name')
            ->whereBelongsTo($admin->company)
            ->when($filters['user_search'], function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query
                        ->whereLike('name', "%{$search}%")
                        ->orWhereLike('email', "%{$search}%")
                        ->orWhereLike('nic', "%{$search}%")
                        ->orWhereLike('job_title', "%{$search}%");
                });
            })
            ->when(in_array($filters['status'], ['active', 'suspended'], true), fn (Builder $query): Builder => $query->where('status', $filters['status']))
            ->when($filters['role'], fn (Builder $query, string $role): Builder => $query->role($role))
            ->latest('id')
            ->paginate(10, ['*'], 'users_page')
            ->withQueryString();

        return $this->formatPaginator($users, fn (User $user): array => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'nic' => $user->nic,
            'phone' => $user->phone,
            'job_title' => $user->job_title,
            'status' => $user->status,
            'roles' => $user->roles->pluck('name')->values(),
            'email_verified_at' => $user->email_verified_at?->toIso8601String(),
            'last_login_at' => $user->last_login_at?->toIso8601String(),
            'created_at' => $user->created_at?->toIso8601String(),
            'can' => [
                'update' => ! $user->hasRole(SystemRoles::ADMIN),
                'delete' => $user->id !== $admin->id && ! $user->hasRole(SystemRoles::ADMIN),
                'suspend' => $user->id !== $admin->id && ! $user->hasRole(SystemRoles::ADMIN),
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    private function roles(Request $request, array $filters): array
    {
        $roles = Role::query()
            ->with('permissions:id,name')
            ->withCount('users')
            ->when($filters['role_search'], fn (Builder $query, string $search): Builder => $query->whereLike('name', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10, ['*'], 'roles_page')
            ->withQueryString();

        return $this->formatPaginator($roles, fn (Role $role): array => [
            'id' => $role->id,
            'name' => $role->name,
            'users_count' => $role->users_count,
            'permissions' => $role->permissions->pluck('name')->values(),
            'protected' => $role->name === SystemRoles::ADMIN,
            'can' => [
                'update' => $role->name !== SystemRoles::ADMIN,
                'delete' => $role->name !== SystemRoles::ADMIN && $role->users_count === 0,
            ],
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     * @return array<string, mixed>
     */
    private function permissions(Request $request, array $filters): array
    {
        $permissions = Permission::query()
            ->withCount('roles')
            ->when($filters['permission_search'], fn (Builder $query, string $search): Builder => $query->whereLike('name', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10, ['*'], 'permissions_page')
            ->withQueryString();

        return $this->formatPaginator($permissions, fn (Permission $permission): array => [
            'id' => $permission->id,
            'name' => $permission->name,
            'roles_count' => $permission->roles_count,
            'protected' => in_array($permission->name, SystemPermissions::all(), true),
            'can' => [
                'update' => ! in_array($permission->name, SystemPermissions::all(), true),
                'delete' => ! in_array($permission->name, SystemPermissions::all(), true) && $permission->roles_count === 0,
            ],
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function assignableRoles(): array
    {
        return Role::query()
            ->where('name', '!=', SystemRoles::ADMIN)
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    private function allPermissions(): array
    {
        return Permission::query()
            ->orderBy('name')
            ->pluck('name')
            ->values()
            ->all();
    }

    /**
     * @return array<string, int>
     */
    private function stats(Request $request): array
    {
        /** @var User $admin */
        $admin = $request->user();

        return [
            'users' => User::query()->whereBelongsTo($admin->company)->count(),
            'activeUsers' => User::query()->whereBelongsTo($admin->company)->where('status', 'active')->count(),
            'roles' => Role::query()->count(),
            'permissions' => Permission::query()->count(),
        ];
    }

    /**
     * @template TModel
     *
     * @param  LengthAwarePaginator<TModel>  $paginator
     * @param  callable(TModel): array<string, mixed>  $map
     * @return array<string, mixed>
     */
    private function formatPaginator(LengthAwarePaginator $paginator, callable $map): array
    {
        return [
            'data' => $paginator->getCollection()->map($map)->values(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'from' => $paginator->firstItem(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'to' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ],
            'links' => [
                'first' => $paginator->url(1),
                'prev' => $paginator->previousPageUrl(),
                'next' => $paginator->nextPageUrl(),
                'last' => $paginator->url($paginator->lastPage()),
            ],
        ];
    }
}
