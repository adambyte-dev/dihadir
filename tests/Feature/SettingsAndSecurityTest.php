<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsAndSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password' => bcrypt('password123'),
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => bcrypt('password123'),
        ]);
    }

    public function test_admin_can_view_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pengaturan.index'));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan');
        $response->assertSee('Data Profil Pengelola');
        $response->assertSee($this->admin->email);
    }

    public function test_admin_can_update_profile_details(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pengaturan.profile'), [
            'name' => 'Nama Baru Admin',
            'email' => 'adminbaru@smkn1majalengka.sch.id',
            'nip' => '198001012005011003',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index', ['tab' => 'profil']));
        $this->admin->refresh();

        $this->assertEquals('Nama Baru Admin', $this->admin->name);
        $this->assertEquals('adminbaru@smkn1majalengka.sch.id', $this->admin->email);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'profile_updated',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_change_password_with_valid_current_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pengaturan.profile'), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'current_password' => 'password123',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect();
        $this->admin->refresh();

        $this->assertTrue(Hash::check('newpassword123', $this->admin->password));
    }

    public function test_admin_cannot_change_password_with_invalid_current_password(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pengaturan.profile'), [
            'name' => $this->admin->name,
            'email' => $this->admin->email,
            'current_password' => 'wrongcurrentpassword',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->admin->refresh();

        $this->assertTrue(Hash::check('password123', $this->admin->password));
    }

    public function test_super_admin_can_update_system_settings(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('admin.pengaturan.system'), [
            'school_name' => 'SMK Negeri 1 Majalengka Official',
            'school_address' => 'Jl. Raya Tonjong - Pinangraja No. 55',
            'school_phone' => '(0233) 281484',
            'school_email' => 'info@smkn1majalengka.sch.id',
            'headmaster_name' => 'Drs. H. Nono Mardiono, M.Pd.',
            'headmaster_nip' => '19680312 199403 1 008',
            'default_organizer' => 'Panitia SMKN 1 Majalengka',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index', ['tab' => 'sistem']));

        $this->assertEquals('SMK Negeri 1 Majalengka Official', SystemSetting::get('school_name'));
        $this->assertEquals('Drs. H. Nono Mardiono, M.Pd.', SystemSetting::get('headmaster_name'));

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'system_settings_updated',
            'user_id' => $this->superAdmin->id,
        ]);
    }

    public function test_regular_admin_cannot_update_system_settings(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.pengaturan.system'), [
            'school_name' => 'Hacked School Name',
            'school_address' => 'Some address',
            'headmaster_name' => 'Someone',
            'headmaster_nip' => '123456',
            'default_organizer' => 'Hacker',
        ]);

        $response->assertStatus(403);
    }
}
