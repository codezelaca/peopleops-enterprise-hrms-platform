<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use App\Support\SystemPermissions;
use App\Support\SystemRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AccessManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_the_users_and_access_page(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk();
    }

    public function test_non_admin_cannot_view_the_users_and_access_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_a_company_user_with_roles(): void
    {
        $admin = $this->admin();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::EMPLOYEE, 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Nimali Fernando',
            'email' => 'nimali@example.com',
            'nic' => '199012345678',
            'phone' => '+94771234567',
            'job_title' => 'HR Executive',
            'status' => 'active',
            'password' => 'PeopleOps#2026',
            'roles' => [$role->name],
        ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('createdUserCredentials.email', 'nimali@example.com');

        $user = User::query()->where('email', 'nimali@example.com')->firstOrFail();

        $this->assertSame($admin->company_id, $user->company_id);
        $this->assertTrue($user->hasRole(SystemRoles::EMPLOYEE));
        $this->assertDatabaseHas('users', [
            'email' => 'nimali@example.com',
            'nic' => '199012345678',
            'status' => 'active',
        ]);
    }

    public function test_user_creation_requires_unique_email_and_nic(): void
    {
        $admin = $this->admin();
        User::factory()->create([
            'company_id' => $admin->company_id,
            'email' => 'existing@example.com',
            'nic' => 'NIC-001',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Duplicate User',
                'email' => 'existing@example.com',
                'nic' => 'NIC-001',
                'password' => 'PeopleOps#2026',
                'status' => 'active',
                'roles' => [],
            ])
            ->assertSessionHasErrors(['email', 'nic']);
    }

    public function test_admin_role_cannot_be_assigned_to_managed_users(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Blocked Admin',
                'email' => 'blocked@example.com',
                'nic' => 'NIC-002',
                'password' => 'PeopleOps#2026',
                'status' => 'active',
                'roles' => [SystemRoles::ADMIN],
            ])
            ->assertSessionHasErrors(['roles.0']);
    }

    public function test_admin_can_update_a_non_admin_user(): void
    {
        $admin = $this->admin();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::HR_MANAGER, 'guard_name' => 'web']);
        $user = User::factory()->create(['company_id' => $admin->company_id]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Updated User',
                'email' => 'updated@example.com',
                'nic' => 'NIC-003',
                'phone' => '+94770000000',
                'job_title' => 'HR Manager',
                'status' => 'suspended',
                'password' => '',
                'roles' => [$role->name],
            ])
            ->assertRedirect();

        $user->refresh();

        $this->assertSame('Updated User', $user->name);
        $this->assertSame('suspended', $user->status);
        $this->assertTrue($user->hasRole(SystemRoles::HR_MANAGER));
    }

    public function test_admin_cannot_delete_the_protected_admin_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHas('error');

        $this->assertModelExists($admin);
    }

    public function test_admin_can_soft_delete_non_admin_user(): void
    {
        $admin = $this->admin();
        $user = User::factory()->create(['company_id' => $admin->company_id]);

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $user))
            ->assertRedirect();

        $this->assertSoftDeleted($user);
    }

    public function test_admin_can_create_and_update_role_permissions(): void
    {
        $admin = $this->admin();
        $permission = Permission::query()->firstOrCreate(['name' => 'employees.manage', 'guard_name' => 'web']);

        $this->actingAs($admin)
            ->post(route('admin.roles.store'), [
                'name' => 'people-ops-lead',
                'permissions' => [$permission->name],
            ])
            ->assertRedirect();

        $role = Role::query()->where('name', 'people-ops-lead')->firstOrFail();

        $this->assertTrue($role->hasPermissionTo('employees.manage'));
    }

    public function test_admin_role_cannot_be_edited_or_deleted(): void
    {
        $admin = $this->admin();
        $role = Role::query()->where('name', SystemRoles::ADMIN)->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.roles.update', $role), [
                'name' => 'admin-renamed',
                'permissions' => [],
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertForbidden();
    }

    public function test_system_permission_cannot_be_edited_or_deleted(): void
    {
        $admin = $this->admin();
        $permission = Permission::query()->where('name', SystemPermissions::all()[0])->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.permissions.update', $permission), [
                'name' => 'renamed.permission',
            ])
            ->assertForbidden();

        $this->actingAs($admin)
            ->delete(route('admin.permissions.destroy', $permission))
            ->assertForbidden();
    }

    public function test_suspended_user_is_logged_out_from_protected_routes(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);

        $this->actingAs($user)
            ->get(route('profile.edit'))
            ->assertRedirect(route('login'));
    }

    private function admin(): User
    {
        $admin = User::factory()->create();
        $company = Company::factory()->create(['setup_by_user_id' => $admin->id]);
        $admin->forceFill(['company_id' => $company->id])->save();

        $permissions = collect(SystemPermissions::all())
            ->map(fn (string $permission): Permission => Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]));

        $role = Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        $role->syncPermissions($permissions);
        $admin->assignRole($role);

        return $admin;
    }
}
