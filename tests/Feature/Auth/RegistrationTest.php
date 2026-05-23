<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Support\SystemRoles;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole(SystemRoles::ADMIN));
        $this->assertDatabaseHas('system_bootstraps', [
            'id' => 1,
            'registered_by_email' => 'test@example.com',
        ]);
    }

    public function test_registration_closes_after_first_user_exists()
    {
        Role::query()->firstOrCreate(['name' => SystemRoles::ADMIN, 'guard_name' => 'web']);
        User::factory()->create();

        $this->get(route('register'))
            ->assertRedirect(route('login'));

        $response = $this->post(route('register.store'), [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'second@example.com']);
    }
}
