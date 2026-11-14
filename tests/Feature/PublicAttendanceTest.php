<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicAttendanceTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Meeting $meeting;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->meeting = Meeting::create([
            'title' => 'Rapat Koordinasi Komite Sekolah Semester Genap',
            'slug' => 'rapat-koordinasi-komite-sekolah-semester-genap',
            'organizer' => 'SMKN 1 Majalengka',
            'meeting_date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '12:00:00',
            'location' => 'Aula Sasana Yudha SMKN 1 Majalengka',
            'form_status' => 'published',
            'event_status' => 'ongoing',
            'allow_anonymous' => true,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_public_attendance_form_renders_successfully_for_published_meeting(): void
    {
        $response = $this->get(route('absensi.form', $this->meeting->public_token));

        $response->assertStatus(200);
        $response->assertSee('Rapat Koordinasi Komite Sekolah Semester Genap');
        $response->assertSee('Aula Sasana Yudha SMKN 1 Majalengka');
        $response->assertSee('Tanda Tangan Digital');
        $response->assertSee('Kirim Absensi');
    }

    public function test_public_attendance_form_blocks_access_when_draft(): void
    {
        $this->meeting->update(['form_status' => 'draft']);

        $response = $this->get(route('absensi.form', $this->meeting->public_token));

        $response->assertStatus(403);
        $response->assertSee('Formulir Belum Dibuka');
    }

    public function test_public_attendance_form_blocks_access_when_closed(): void
    {
        $this->meeting->update(['form_status' => 'closed']);

        $response = $this->get(route('absensi.form', $this->meeting->public_token));

        $response->assertStatus(403);
        $response->assertSee('Formulir Telah Ditutup');
    }

    public function test_non_anonymous_meeting_redirects_unauthenticated_participant_to_login(): void
    {
        $this->meeting->update(['allow_anonymous' => false]);

        $response = $this->get(route('absensi.form', $this->meeting->public_token));

        $response->assertRedirect(route('absensi.meeting.login', $this->meeting->public_token));
    }

    public function test_authenticated_participant_sees_prefilled_data_on_form(): void
    {
        $this->meeting->update(['allow_anonymous' => false]);

        $participant = Participant::create([
            'nip' => '198805202011011005',
            'name' => 'Dra. Hj. Siti Rohmah, M.Pd.',
            'unit_kerja' => 'Cabang Dinas Pendidikan Wilayah IX',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'phone' => '081234567890',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($participant, 'participant')
            ->get(route('absensi.form', $this->meeting->public_token));

        $response->assertStatus(200);
        $response->assertSee('198805202011011005');
        $response->assertSee('Dra. Hj. Siti Rohmah, M.Pd.');
    }

    public function test_participant_can_submit_valid_attendance_with_signature(): void
    {
        // 1x1 transparent pixel PNG base64
        $fakeBase64Sig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $payload = [
            'name' => 'Ahmad Fauzi, S.Pd.',
            'nip' => '199201152019031008',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature' => $fakeBase64Sig,
        ];

        $response = $this->post(route('absensi.submit', $this->meeting->public_token), $payload);

        $response->assertRedirect(route('absensi.success', $this->meeting->public_token));

        $this->assertDatabaseHas('attendances', [
            'meeting_id' => $this->meeting->id,
            'nip' => '199201152019031008',
            'name' => 'Ahmad Fauzi, S.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'status' => 'valid',
        ]);

        $attendance = Attendance::where('meeting_id', $this->meeting->id)->first();
        $this->assertNotNull($attendance->signature_path);
        Storage::disk('local')->assertExists($attendance->signature_path);
    }

    public function test_anti_duplicate_attendance_submission_rejected(): void
    {
        $fakeBase64Sig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';

        $payload = [
            'name' => 'Ahmad Fauzi, S.Pd.',
            'nip' => '199201152019031008',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature' => $fakeBase64Sig,
        ];

        // First submission succeeds
        $this->post(route('absensi.submit', $this->meeting->public_token), $payload)
            ->assertRedirect(route('absensi.success', $this->meeting->public_token));

        // Second submission with exact same NIP on the same meeting must fail
        $response = $this->from(route('absensi.form', $this->meeting->public_token))
            ->post(route('absensi.submit', $this->meeting->public_token), $payload);

        $response->assertRedirect(route('absensi.form', $this->meeting->public_token));
        $response->assertSessionHas('error');

        // Verify count remains 1
        $this->assertEquals(1, Attendance::where('meeting_id', $this->meeting->id)->count());
    }

    public function test_attendance_success_page_renders_confirmation(): void
    {
        $response = $this->get(route('absensi.success', $this->meeting->public_token));

        $response->assertStatus(200);
        $response->assertSee('Kehadiran Berhasil Dicatat');
        $response->assertSee($this->meeting->title);
    }
}
