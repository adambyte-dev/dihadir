<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserAndMeetingParticipantTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected Meeting $meeting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@smkn1majalengka.sch.id',
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin Guru',
            'email' => 'guru@smkn1majalengka.sch.id',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->meeting = Meeting::create([
            'title' => 'Rapat Pleno Komite Sekolah',
            'organizer' => 'Komite SMKN 1 Majalengka',
            'location' => 'Aula Utama SMKN 1 Majalengka',
            'meeting_date' => now()->addDays(2)->toDateString(),
            'start_time' => '08:00',
            'status' => 'scheduled',
            'form_status' => 'published',
            'created_by' => $this->superAdmin->id,
        ]);
    }

    public function test_regular_admin_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_super_admin_can_view_user_management_list(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.users.index'));
        $response->assertStatus(200);
        $response->assertSee('Kelola Akun Pengelola');
        $response->assertSee('Admin Guru');
    }

    public function test_super_admin_can_create_new_admin_user(): void
    {
        $response = $this->actingAs($this->superAdmin)->post(route('admin.users.store'), [
            'name' => 'Staf Tata Usaha Baru',
            'email' => 'tu_baru@smkn1majalengka.sch.id',
            'role' => 'admin',
            'is_active' => '1',
            'password' => 'P@ssw0rd123',
            'password_confirmation' => 'P@ssw0rd123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'tu_baru@smkn1majalengka.sch.id',
            'name' => 'Staf Tata Usaha Baru',
            'role' => 'admin',
        ]);
    }

    public function test_super_admin_cannot_delete_their_own_account(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->superAdmin));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_admin_can_invite_participants_to_meeting(): void
    {
        $p1 = Participant::create([
            'nip' => '198501012010011001',
            'name' => 'Budi Santoso, S.Pd.',
            'unit_kerja' => 'Guru Rekayasa Perangkat Lunak',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $p2 = Participant::create([
            'nip' => '198802022012012002',
            'name' => 'Siti Nurhaliza, M.Pd.',
            'unit_kerja' => 'Guru Bahasa Indonesia',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.pertemuan.peserta.store', $this->meeting),
            ['participant_ids' => [$p1->id, $p2->id]]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_participants', [
            'meeting_id' => $this->meeting->id,
            'participant_id' => $p1->id,
            'status' => 'invited',
        ]);
        $this->assertDatabaseHas('meeting_participants', [
            'meeting_id' => $this->meeting->id,
            'participant_id' => $p2->id,
            'status' => 'invited',
        ]);
    }

    public function test_admin_can_sync_all_active_participants_to_meeting(): void
    {
        Participant::create([
            'nip' => '199003032015011003',
            'name' => 'Ahmad Fauzi',
            'unit_kerja' => 'Tata Usaha',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->superAdmin)->post(
            route('admin.pertemuan.peserta.sync-all', $this->meeting)
        );

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $expectedCount = Participant::where('is_active', true)->count();
        $this->assertEquals($expectedCount, $this->meeting->participants()->count());
    }

    public function test_local_svg_qr_code_endpoint_renders_valid_svg(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.pertemuan.qr', $this->meeting));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
        $this->assertStringContainsString('<svg', $response->getContent());
    }

    public function test_local_svg_qr_download_endpoint_returns_attachment(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.pertemuan.qr.download', $this->meeting));
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="QR-' . $this->meeting->slug . '.svg"');
        $this->assertStringContainsString('<svg', $response->getContent());
    }
}
