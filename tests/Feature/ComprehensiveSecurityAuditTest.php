<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveSecurityAuditTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $otherAdmin;
    private User $superAdmin;
    private Meeting $meeting;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password' => bcrypt('password123'),
        ]);

        $this->otherAdmin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
            'password' => bcrypt('password123'),
        ]);

        $this->superAdmin = User::factory()->create([
            'role' => 'super_admin',
            'is_active' => true,
            'password' => bcrypt('password123'),
        ]);

        $this->meeting = Meeting::create([
            'title' => 'Pertemuan Rahasia Komite',
            'slug' => 'pertemuan-rahasia-komite',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula Utama',
            'meeting_date' => now()->addDays(2)->toDateString(),
            'start_time' => '08:00',
            'status' => 'scheduled',
            'form_status' => 'published',
            'allow_anonymous' => true,
            'public_token' => 'meetingsecrettoken1234567890abcd',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_guest_is_redirected_when_accessing_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_cannot_access_audit_logs(): void
    {
        $response = $this->get(route('admin.audit.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_regular_admin_cannot_access_super_admin_audit_logs(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.audit.index'));
        $response->assertStatus(403);
    }

    public function test_regular_admin_cannot_access_user_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_idor_protection_admin_cannot_view_or_edit_unassigned_meeting(): void
    {
        // otherAdmin tidak memiliki hak akses ke pertemuan milik admin
        $response = $this->actingAs($this->otherAdmin)->get(route('admin.pertemuan.show', $this->meeting));
        $response->assertStatus(403);

        $responseEdit = $this->actingAs($this->otherAdmin)->get(route('admin.pertemuan.edit', $this->meeting));
        $responseEdit->assertStatus(403);

        $responseUpdate = $this->actingAs($this->otherAdmin)->put(route('admin.pertemuan.update', $this->meeting), [
            'title' => 'Hijacked Title',
            'status' => 'ongoing',
            'form_status' => 'published',
        ]);
        $responseUpdate->assertStatus(403);
    }

    public function test_super_admin_can_access_any_meeting(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('admin.pertemuan.show', $this->meeting));
        $response->assertStatus(200);
    }

    public function test_storage_local_route_is_disabled_and_does_not_leak_private_files(): void
    {
        // Route storage.local sebelumnya mengekspos rute PUT/GET storage/{path}
        $response = $this->get('/storage/signatures/test.png');
        // Tidak ada route terdaftar yang menangani GET /storage/{path} selain asset statis 404
        $this->assertTrue(in_array($response->status(), [404, 403], true));
    }

    public function test_security_headers_are_present_in_responses(): void
    {
        // Rute privat/login admin: wajib noindex
        $response = $this->get(route('admin.login'));

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
        $response->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');

        // Rute publik: diizinkan terindeks
        $publicResponse = $this->get(route('landing'));
        $this->assertStringContainsString('index, follow', (string) $publicResponse->headers->get('X-Robots-Tag'));
    }

    public function test_csv_formula_injection_sanitization(): void
    {
        $sanitizedEqual = \App\Http\Controllers\Admin\ReportController::sanitizeCsvValue('=1+1');
        $this->assertEquals("'=1+1", $sanitizedEqual);

        $sanitizedCmd = \App\Http\Controllers\Admin\ReportController::sanitizeCsvValue('+2+3');
        $this->assertEquals("'+2+3", $sanitizedCmd);

        $sanitizedAt = \App\Http\Controllers\Admin\ReportController::sanitizeCsvValue('@SUM(A1:A10)');
        $this->assertEquals("'@SUM(A1:A10)", $sanitizedAt);

        $sanitizedMinus = \App\Http\Controllers\Admin\ReportController::sanitizeCsvValue('-10');
        $this->assertEquals("'-10", $sanitizedMinus);

        $normalText = \App\Http\Controllers\Admin\ReportController::sanitizeCsvValue('Budi Santoso');
        $this->assertEquals('Budi Santoso', $normalText);
    }

    public function test_malicious_signature_file_upload_rejected(): void
    {
        $meetingToken = $this->meeting->public_token;

        $response = $this->from(route('absensi.form', $meetingToken))
            ->post(route('absensi.submit', $meetingToken), [
                'nip' => '198501012010011001',
                'name' => 'Peserta Uji',
                'unit_kerja' => 'SMKN 1 Majalengka',
                'kabupaten_kota' => 'Kabupaten Majalengka',
                'signature' => 'data:image/jpeg;base64,evilscript',
            ]);

        $response->assertSessionHasErrors('signature');
        $this->assertDatabaseMissing('attendances', [
            'meeting_id' => $this->meeting->id,
            'nip' => '198501012010011001',
        ]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $response = $this->actingAs($this->superAdmin)->delete(route('admin.users.destroy', $this->superAdmin));
        $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        $this->assertDatabaseHas('users', ['id' => $this->superAdmin->id]);
    }

    public function test_admin_cannot_demote_sole_super_admin(): void
    {
        $response = $this->actingAs($this->superAdmin)->put(route('admin.users.update', $this->superAdmin), [
            'name' => $this->superAdmin->name,
            'email' => $this->superAdmin->email,
            'role' => 'admin', // Coba turunkan peran ke admin biasa
            'is_active' => true,
        ]);

        $response->assertSessionHas('error');
        $this->superAdmin->refresh();
        $this->assertEquals('super_admin', $this->superAdmin->role);
    }
}
