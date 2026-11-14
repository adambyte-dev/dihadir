<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\AuditLog;
use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RekapAndReportingTest extends TestCase
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
            'title' => 'Rapat Pleno Dewan Guru & Karyawan',
            'slug' => 'rapat-pleno-dewan-guru-karyawan',
            'organizer' => 'SMKN 1 Majalengka',
            'meeting_date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '11:00:00',
            'location' => 'Aula Sasana Yudha',
            'form_status' => 'published',
            'event_status' => 'ongoing',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_rekap_kehadiran_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index'));

        $response->assertStatus(200);
        $response->assertSee('Rekap Kehadiran');
        $response->assertSee('Rapat Pleno Dewan Guru', false);
    }

    public function test_rekap_kehadiran_displays_attendances_and_metrics(): void
    {
        // Create an attendance record
        $fakeSigPath = 'signatures/' . $this->meeting->id . '/sig_test.png';
        Storage::disk('local')->put($fakeSigPath, 'fake_png_data');

        Attendance::create([
            'meeting_id' => $this->meeting->id,
            'nip' => '198501012010011001',
            'name' => 'Budi Santoso, S.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature_path' => $fakeSigPath,
            'attended_date' => now()->toDateString(),
            'attended_time' => '08:15:00',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index', ['meeting_id' => $this->meeting->id]));

        $response->assertStatus(200);
        $response->assertSee('Budi Santoso, S.Pd.');
        $response->assertSee('198501012010011001');
    }

    public function test_rekap_tab_belum_hadir_lists_invited_participants(): void
    {
        $participant = Participant::create([
            'nip' => '199002022015022002',
            'name' => 'Dewi Lestari, M.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        // Attach to meeting as invited
        $this->meeting->participants()->attach($participant->id, ['status' => 'invited']);

        $response = $this->actingAs($this->admin)->get(route('admin.rekap.index', [
            'meeting_id' => $this->meeting->id,
            'tab' => 'belum_hadir',
        ]));

        $response->assertStatus(200);
        $response->assertSee('Dewi Lestari, M.Pd.');
        $response->assertSee('199002022015022002');
    }

    public function test_admin_can_update_attendance_status_and_audit_log_is_recorded(): void
    {
        $attendance = Attendance::create([
            'meeting_id' => $this->meeting->id,
            'nip' => '198501012010011001',
            'name' => 'Budi Santoso, S.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature_path' => 'signatures/' . $this->meeting->id . '/sig_test.png',
            'attended_date' => now()->toDateString(),
            'attended_time' => '08:15:00',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.rekap.status', $attendance), [
            'status' => 'flagged',
            'reason' => 'Perlu verifikasi tanda tangan',
        ]);

        $response->assertRedirect();
        $this->assertEquals('flagged', $attendance->fresh()->status);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'attendance_status_updated',
            'target_type' => Attendance::class,
            'target_id' => $attendance->id,
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_signature_securely(): void
    {
        $fakeSigPath = 'signatures/' . $this->meeting->id . '/sig_test.png';
        Storage::disk('local')->put($fakeSigPath, 'fake_png_data');

        $attendance = Attendance::create([
            'meeting_id' => $this->meeting->id,
            'nip' => '198501012010011001',
            'name' => 'Budi Santoso, S.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature_path' => $fakeSigPath,
            'attended_date' => now()->toDateString(),
            'attended_time' => '08:15:00',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.rekap.signature', $attendance));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/png');
    }

    public function test_admin_can_view_reports_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Laporan Kehadiran');
        $response->assertSee($this->meeting->title);
    }

    public function test_admin_can_export_pdf_report(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.pdf', $this->meeting));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'report_exported_pdf',
            'user_id' => $this->admin->id,
            'target_id' => $this->meeting->id,
        ]);
    }

    public function test_admin_can_export_csv_with_formula_sanitization(): void
    {
        // Add attendance with potentially malicious characters (=SUM, @admin, +calc)
        Attendance::create([
            'meeting_id' => $this->meeting->id,
            'nip' => '=12345678',
            'name' => '+Injected Name',
            'unit_kerja' => '@Dangerous Instansi',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature_path' => 'signatures/' . $this->meeting->id . '/sig_test.png',
            'attended_date' => now()->toDateString(),
            'attended_time' => '08:30:00',
            'status' => 'valid',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.laporan.csv', $this->meeting));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();

        // Formula characters must be sanitized with single quote prefix
        $this->assertStringContainsString("'=12345678", $content);
        $this->assertStringContainsString("'+Injected Name", $content);
        $this->assertStringContainsString("'@Dangerous Instansi", $content);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'report_exported_csv',
            'user_id' => $this->admin->id,
            'target_id' => $this->meeting->id,
        ]);
    }

    public function test_admin_can_view_print_preview(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.laporan.print', $this->meeting));

        $response->assertStatus(200);
        $response->assertSee('Daftar Hadir Kegiatan Pertemuan');
        $response->assertSee('SMK Negeri 1 Majalengka');
        $response->assertSee($this->meeting->title);
    }
}
