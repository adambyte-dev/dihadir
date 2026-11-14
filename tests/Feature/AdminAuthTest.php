<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_landing_page_or_can_access_admin_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('DIHADIR');
    }

    public function test_admin_login_page_renders_successfully(): void
    {
        $response = $this->get(route('admin.login'));
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Panel Admin');
    }

    public function test_super_admin_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1majalengka.sch.id',
            'password' => Hash::make('DihadirAdmin2026!'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'login' => 'admin@smkn1majalengka.sch.id',
            'password' => 'DihadirAdmin2026!',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_with_invalid_password(): void
    {
        User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@smkn1majalengka.sch.id',
            'password' => Hash::make('DihadirAdmin2026!'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'login' => 'admin@smkn1majalengka.sch.id',
            'password' => 'WrongPassword',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_inactive_admin_cannot_login(): void
    {
        User::create([
            'name' => 'Nonaktif Admin',
            'email' => 'nonaktif@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => false,
        ]);

        $response = $this->post(route('admin.login.submit'), [
            'login' => 'nonaktif@smkn1majalengka.sch.id',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('login');
        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_logout(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'test@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.logout'));

        $response->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }
}
