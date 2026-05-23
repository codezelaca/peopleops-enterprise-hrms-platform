<?php

namespace Tests\Feature\Onboarding;

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CompanyOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_complete_company_onboarding(): void
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        $user->assignRole($role);

        $response = $this->actingAs($user)->post(route('onboarding.company.store'), [
            'name' => 'Codezela PeopleOps',
            'legal_name' => 'Codezela PeopleOps Private Limited',
            'registration_number' => 'PV-12345',
            'tax_id' => 'TIN-98765',
            'industry' => 'Software Services',
            'company_size' => '11-50',
            'website' => 'https://example.com',
            'support_email' => 'hr@example.com',
            'phone' => '+94112345678',
            'timezone' => 'Asia/Colombo',
            'country' => 'lk',
            'city' => 'Colombo',
            'address_line_1' => '12 Marine Drive',
            'address_line_2' => 'Level 4',
            'postal_code' => '00300',
            'default_currency' => 'lkr',
            'fiscal_year_start_month' => 1,
            'work_week_starts_on' => 'monday',
            'logo' => UploadedFile::fake()->image('logo.png', 320, 320),
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('companies', [
            'name' => 'Codezela PeopleOps',
            'country' => 'LK',
            'default_currency' => 'LKR',
        ]);

        $this->assertNotNull($user->refresh()->company_id);
        Storage::disk('local')->assertExists($user->company->logo_path);
    }

    public function test_onboarding_requires_valid_company_data(): void
    {
        $user = User::factory()->create();
        $role = Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        $user->assignRole($role);

        $this->actingAs($user)
            ->post(route('onboarding.company.store'), [
                'name' => '',
                'support_email' => 'not-an-email',
                'country' => 'LKA',
                'default_currency' => 'LKR',
            ])
            ->assertSessionHasErrors(['name', 'legal_name', 'support_email', 'country']);
    }
}
