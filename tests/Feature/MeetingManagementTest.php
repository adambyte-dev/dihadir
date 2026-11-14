<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MeetingManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;
    private User $admin1;
    private User $admin2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->admin1 = User::create([
            'name' => 'Admin Panitia 1',
            'email' => 'panitia1@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->admin2 = User::create([
            'name' => 'Admin Panitia 2',
            'email' => 'panitia2@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_meetings_index_page(): void
    {
        $response = $this->actingAs($this->admin1)->get(route('admin.pertemuan.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Pertemuan');
        $response->assertSee('Belum ada kegiatan pertemuan yang terdaftar');
    }

    public function test_admin_can_create_new_meeting(): void
    {
        $response = $this->actingAs($this->admin1)->post(route('admin.pertemuan.store'), [
            'title' => 'Rapat Pleno Komite Kelas X',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula Utama',
            'meeting_date' => '2026-11-20',
            'start_time' => '08:00',
            'end_time' => '11:30',
            'status' => 'scheduled',
            'form_status' => 'draft',
            'allow_anonymous' => true,
        ]);

        $meeting = Meeting::first();

        $this->assertNotNull($meeting);
        $this->assertEquals('Rapat Pleno Komite Kelas X', $meeting->title);
        $this->assertEquals($this->admin1->id, $meeting->created_by);
        $this->assertNotEmpty($meeting->slug);
        $this->assertNotEmpty($meeting->public_token);
        $this->assertEquals(32, strlen($meeting->public_token));

        $response->assertRedirect(route('admin.pertemuan.show', $meeting));
    }

    public function test_admin_can_view_own_meeting_details(): void
    {
        $meeting = Meeting::create([
            'title' => 'Rapat Dewan Guru',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Ruang Guru',
            'meeting_date' => '2026-11-21',
            'start_time' => '09:00',
            'status' => 'scheduled',
            'form_status' => 'published',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->admin1)->get(route('admin.pertemuan.show', $meeting));

        $response->assertStatus(200);
        $response->assertSee('Rapat Dewan Guru');
        $response->assertSee($meeting->public_token);
    }

    public function test_admin_cannot_view_or_edit_another_admins_meeting(): void
    {
        $meeting = Meeting::create([
            'title' => 'Rapat Rahasia Admin 1',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Ruang Kepala Sekolah',
            'meeting_date' => '2026-11-22',
            'start_time' => '10:00',
            'status' => 'scheduled',
            'form_status' => 'draft',
            'created_by' => $this->admin1->id,
        ]);

        // Admin 2 mencoba melihat detail pertemuan Admin 1
        $response = $this->actingAs($this->admin2)->get(route('admin.pertemuan.show', $meeting));
        $response->assertStatus(403);

        // Admin 2 mencoba mengedit pertemuan Admin 1
        $editResponse = $this->actingAs($this->admin2)->get(route('admin.pertemuan.edit', $meeting));
        $editResponse->assertStatus(403);
    }

    public function test_super_admin_can_view_and_manage_any_meeting(): void
    {
        $meeting = Meeting::create([
            'title' => 'Pertemuan Dibuat Admin Biasa',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Lab RPL',
            'meeting_date' => '2026-11-25',
            'start_time' => '13:00',
            'status' => 'scheduled',
            'form_status' => 'draft',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('admin.pertemuan.show', $meeting));
        $response->assertStatus(200);
        $response->assertSee('Pertemuan Dibuat Admin Biasa');
    }

    public function test_admin_can_update_meeting(): void
    {
        $meeting = Meeting::create([
            'title' => 'Workshop Kurikulum Merdeka',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula',
            'meeting_date' => '2026-11-26',
            'start_time' => '08:30',
            'status' => 'scheduled',
            'form_status' => 'draft',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->admin1)->put(route('admin.pertemuan.update', $meeting), [
            'title' => 'Workshop Kurikulum Merdeka Revisi',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula Gedung B',
            'meeting_date' => '2026-11-27',
            'start_time' => '09:00',
            'status' => 'ongoing',
            'form_status' => 'published',
        ]);

        $meeting->refresh();

        $this->assertEquals('Workshop Kurikulum Merdeka Revisi', $meeting->title);
        $this->assertEquals('Aula Gedung B', $meeting->location);
        $this->assertEquals('ongoing', $meeting->status);
        $this->assertEquals('published', $meeting->form_status);

        $response->assertRedirect(route('admin.pertemuan.show', $meeting));
    }

    public function test_admin_can_toggle_form_status(): void
    {
        $meeting = Meeting::create([
            'title' => 'Rapat Orang Tua Siswa',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula',
            'meeting_date' => '2026-11-28',
            'start_time' => '08:00',
            'status' => 'ongoing',
            'form_status' => 'draft',
            'created_by' => $this->admin1->id,
        ]);

        // Toggle dari draft ke published
        $this->actingAs($this->admin1)->post(route('admin.pertemuan.toggle-form', $meeting));
        $meeting->refresh();
        $this->assertEquals('published', $meeting->form_status);

        // Toggle dari published ke closed
        $this->actingAs($this->admin1)->post(route('admin.pertemuan.toggle-form', $meeting));
        $meeting->refresh();
        $this->assertEquals('closed', $meeting->form_status);
    }

    public function test_admin_can_duplicate_meeting(): void
    {
        $meeting = Meeting::create([
            'title' => 'Rapat Koordinasi Mingguan',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Ruang Guru',
            'meeting_date' => '2026-11-15',
            'start_time' => '07:30',
            'status' => 'completed',
            'form_status' => 'closed',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->admin1)->post(route('admin.pertemuan.duplicate', $meeting));

        $this->assertEquals(2, Meeting::count());

        $duplicated = Meeting::where('title', 'like', '%(Salinan)%')->first();

        $this->assertNotNull($duplicated);
        $this->assertNotEquals($meeting->slug, $duplicated->slug);
        $this->assertNotEquals($meeting->public_token, $duplicated->public_token);
        $this->assertEquals('scheduled', $duplicated->status);
        $this->assertEquals('draft', $duplicated->form_status);

        $response->assertRedirect(route('admin.pertemuan.edit', $duplicated));
    }

    public function test_admin_can_soft_delete_meeting(): void
    {
        $meeting = Meeting::create([
            'title' => 'Pertemuan Dibatalkan',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula',
            'meeting_date' => '2026-11-30',
            'start_time' => '10:00',
            'status' => 'draft',
            'form_status' => 'draft',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->admin1)->delete(route('admin.pertemuan.destroy', $meeting));

        $response->assertRedirect(route('admin.pertemuan.index'));
        $this->assertSoftDeleted('meetings', ['id' => $meeting->id]);
    }

    public function test_dashboard_displays_real_meeting_count(): void
    {
        Meeting::create([
            'title' => 'Acara 1',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula',
            'meeting_date' => '2026-11-14',
            'start_time' => '08:00',
            'created_by' => $this->admin1->id,
        ]);

        Meeting::create([
            'title' => 'Acara 2',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Lab',
            'meeting_date' => '2026-11-15',
            'start_time' => '09:00',
            'created_by' => $this->admin1->id,
        ]);

        $response = $this->actingAs($this->admin1)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Total Pertemuan');
        // Angka 2 tercatat di kartu statistik
        $response->assertSee('2');
        $response->assertSee('Acara 1');
        $response->assertSee('Acara 2');
    }
}
