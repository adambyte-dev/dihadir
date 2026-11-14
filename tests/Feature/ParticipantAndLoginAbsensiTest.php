<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\Participant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ParticipantAndLoginAbsensiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Participant $participant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Pengelola',
            'email' => 'admin@smkn1majalengka.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->participant = Participant::create([
            'nip' => '198501152010011002',
            'name' => 'Budi Santoso, S.Pd.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kab. Majalengka',
            'phone_number' => '081234567890',
            'password' => Hash::make('rahasia123'),
            'is_active' => true,
        ]);
    }

    public function test_admin_can_view_participants_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.peserta.index'));

        $response->assertStatus(200);
        $response->assertSee('Data Peserta');
        $response->assertSee('Budi Santoso, S.Pd.');
        $response->assertSee('198501152010011002');
    }

    public function test_admin_can_create_new_participant(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.peserta.store'), [
            'nip' => '199002142015022001',
            'name' => 'Siti Aminah, M.Kom.',
            'unit_kerja' => 'SMKN 1 Majalengka',
            'kabupaten_kota' => 'Kab. Cirebon',
            'phone_number' => '085712345678',
            'password' => 'passwordBaru',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.peserta.index'));

        $this->assertDatabaseHas('participants', [
            'nip' => '199002142015022001',
            'name' => 'Siti Aminah, M.Kom.',
        ]);
    }

    public function test_admin_can_update_participant(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.peserta.update', $this->participant), [
            'nip' => '198501152010011002',
            'name' => 'Budi Santoso, M.Pd.',
            'unit_kerja' => 'Cabang Dinas Pendidikan IX',
            'kabupaten_kota' => 'Kab. Majalengka',
        ]);

        $response->assertRedirect(route('admin.peserta.index'));

        $this->participant->refresh();
        $this->assertEquals('Budi Santoso, M.Pd.', $this->participant->name);
        $this->assertEquals('Cabang Dinas Pendidikan IX', $this->participant->unit_kerja);
    }

    public function test_admin_can_delete_participant(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('admin.peserta.destroy', $this->participant));

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertSoftDeleted('participants', ['id' => $this->participant->id]);
    }

    public function test_admin_can_import_participants_from_csv(): void
    {
        $csvContent = "NIP,Nama,Unit Kerja,Kabupaten/Kota,Telepon\n"
                    . "197505102000031001,Drs. H. Maman Suratman,SMKN 1 Majalengka,Kab. Majalengka,081299998888\n"
                    . "198808182011012003,Nina Marlina,SMKN 1 Majalengka,Kab. Kuningan,085811112222\n";

        $file = UploadedFile::fake()->createWithContent('peserta.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post(route('admin.peserta.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.peserta.index'));
        $this->assertDatabaseHas('participants', ['nip' => '197505102000031001']);
        $this->assertDatabaseHas('participants', ['nip' => '198808182011012003']);
    }

    public function test_admin_can_download_participant_csv_template(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.peserta.template-csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename="template-impor-peserta.csv"');

        $content = $response->streamedContent();
        $this->assertStringContainsString('nip,name,unit_kerja,kabupaten_kota,phone_number', $content);
        $this->assertStringContainsString('198507152010011003', $content);
        $this->assertStringContainsString('SMK Negeri 1 Majalengka', $content);
    }

    public function test_participant_login_page_renders_successfully(): void
    {
        $response = $this->get(route('absensi.login'));

        $response->assertStatus(200);
        $response->assertSee('Masuk Absensi Pertemuan');
        $response->assertSee('Verifikasi Keamanan');
    }

    public function test_participant_login_fails_without_slider_verification(): void
    {
        $response = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011002',
            'password' => 'rahasia123',
            'slider_token' => '', // Tanpa menggeser slider
        ]);

        $response->assertSessionHasErrors('slider_token');
        $this->assertFalse(Auth::guard('participant')->check());
    }

    public function test_participant_login_fails_with_invalid_credentials(): void
    {
        $token = 'test_valid_slide_token_12345';
        session(['slide_verify_token' => $token]);

        $response = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011002',
            'password' => 'PasswordSalah',
            'slider_token' => $token,
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertFalse(Auth::guard('participant')->check());
    }

    public function test_participant_can_login_with_valid_credentials_and_slider_token(): void
    {
        $token = 'test_valid_slide_token_12345';
        session(['slide_verify_token' => $token]);

        $response = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011002',
            'password' => 'rahasia123',
            'slider_token' => $token,
        ]);

        $this->assertTrue(Auth::guard('participant')->check());
        $this->assertEquals($this->participant->id, Auth::guard('participant')->id());
    }

    public function test_participant_login_redirects_to_meeting_when_meeting_token_provided(): void
    {
        $meeting = Meeting::create([
            'title' => 'Rapat Orang Tua Murid',
            'organizer' => 'SMKN 1 Majalengka',
            'location' => 'Aula',
            'meeting_date' => '2026-11-20',
            'start_time' => '08:00',
            'created_by' => $this->admin->id,
        ]);

        $token = 'test_valid_slide_token_12345';
        session(['slide_verify_token' => $token]);

        $response = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011002',
            'password' => 'rahasia123',
            'slider_token' => $token,
            'meeting_token' => $meeting->public_token,
        ]);

        $response->assertRedirect(route('absensi.form', $meeting->public_token));
    }

    public function test_inactive_participant_cannot_login(): void
    {
        $this->participant->update(['is_active' => false]);

        $token = 'test_valid_slide_token_12345';
        session(['slide_verify_token' => $token]);

        $response = $this->post(route('absensi.login.submit'), [
            'nip' => '198501152010011002',
            'password' => 'rahasia123',
            'slider_token' => $token,
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertFalse(Auth::guard('participant')->check());
    }
}
