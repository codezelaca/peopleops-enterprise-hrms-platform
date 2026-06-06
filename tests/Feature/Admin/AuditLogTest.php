<?php

namespace Tests\Feature\Admin;

use App\Models\Company;
use App\Models\User;
use App\Support\AuditLogPresenter;
use App\Support\SystemPermissions;
use App\Support\SystemRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_audit_logs(): void
    {
        $admin = $this->admin();

        activity()
            ->causedBy($admin)
            ->performedOn($admin)
            ->event('updated')
            ->log('Profile updated');

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('admin/audit/Index')
                ->has('logs.data', 1)
            );
    }

    public function test_non_admin_cannot_view_audit_logs(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }

    public function test_audit_logs_can_be_filtered_by_event_and_search(): void
    {
        $admin = $this->admin();

        activity()
            ->causedBy($admin)
            ->performedOn($admin)
            ->event('updated')
            ->log('Password updated');

        activity()
            ->causedBy($admin)
            ->event('viewed')
            ->log('Dashboard viewed');

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index', [
                'event' => 'updated',
                'search' => 'Password',
            ]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page): AssertableInertia => $page
                ->component('admin/audit/Index')
                ->where('logs.data.0.description', 'Password updated')
                ->has('logs.data', 1)
            );
    }

    public function test_authenticated_page_views_are_recorded(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk();

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Dashboard viewed',
            'event' => 'viewed',
            'causer_type' => User::class,
            'causer_id' => $admin->id,
        ]);
    }

    public function test_role_deletion_is_recorded_before_delete(): void
    {
        $admin = $this->admin();
        $role = Role::query()->create(['name' => 'temporary-reviewer', 'guard_name' => 'web']);

        $this->actingAs($admin)
            ->delete(route('admin.roles.destroy', $role))
            ->assertRedirect();

        $this->assertDatabaseHas('activity_log', [
            'description' => 'Role deleted by administrator',
            'event' => 'deleted',
            'subject_type' => Role::class,
            'subject_id' => $role->id,
        ]);
    }

    public function test_user_phone_update_records_one_clear_audit_entry(): void
    {
        $admin = $this->admin();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::HR_MANAGER, 'guard_name' => 'web']);
        $user = User::factory()->create([
            'company_id' => $admin->company_id,
            'name' => 'Jane Eyre',
            'email' => 'jane.eyre@example.com',
            'nic' => 'NIC-100',
            'phone' => '+94770000001',
            'job_title' => 'HR Manager',
            'status' => 'active',
        ]);
        $user->assignRole($role);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $user), [
                'name' => 'Jane Eyre',
                'email' => 'jane.eyre@example.com',
                'nic' => 'NIC-100',
                'phone' => '+94770000002',
                'job_title' => 'HR Manager',
                'status' => 'active',
                'password' => '',
                'roles' => [$role->name],
            ])
            ->assertRedirect();

        $this->assertSame(1, Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->count());

        /** @var Activity $activity */
        $activity = Activity::query()
            ->where('subject_type', User::class)
            ->where('subject_id', $user->id)
            ->where('event', 'updated')
            ->firstOrFail();

        $this->assertSame('User account updated by administrator', $activity->description);

        $presented = app(AuditLogPresenter::class)->present($activity);

        $this->assertSame([
            [
                'field' => 'Phone',
                'old' => '+94770000001',
                'new' => '+94770000002',
            ],
        ], $presented['changes']);
    }

    public function test_sensitive_values_are_not_exposed_in_presented_changes(): void
    {
        $admin = $this->admin();

        /** @var Activity $activity */
        $activity = activity()
            ->causedBy($admin)
            ->performedOn($admin)
            ->event('updated')
            ->withProperties([
                'old' => ['password' => 'old-secret', 'name' => 'Old Name'],
                'attributes' => ['password' => 'new-secret', 'name' => 'New Name'],
            ])
            ->log('Profile updated');

        $presented = app(AuditLogPresenter::class)->present($activity);

        $this->assertSame('Name', $presented['changes'][0]['field']);
        $this->assertStringNotContainsString('secret', json_encode($presented, JSON_THROW_ON_ERROR));
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
