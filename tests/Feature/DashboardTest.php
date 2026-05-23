<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page()
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_visit_the_dashboard()
    {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        $user->assignRole($role);

        $company = Company::factory()->create(['setup_by_user_id' => $user->id]);
        $user->forceFill(['company_id' => $company->id])->save();

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));
        $response->assertOk();
    }

    public function test_admin_is_redirected_to_company_onboarding_until_setup_is_complete()
    {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('onboarding.company.create'));
    }
}
