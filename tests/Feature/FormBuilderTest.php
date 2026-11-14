<?php

namespace Tests\Feature;

use App\Models\Meeting;
use App\Models\MeetingFormField;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Meeting $meeting;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->meeting = Meeting::create([
            'title' => 'Sosialisasi PKL Industri 2026',
            'organizer' => 'Hubin SMKN 1 Majalengka',
            'location' => 'Aula Gedung B SMKN 1 Majalengka',
            'meeting_date' => now()->toDateString(),
            'start_time' => '09:00',
            'status' => 'scheduled',
            'form_status' => 'published',
            'allow_anonymous' => true,
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_view_form_builder(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pertemuan.builder.index', $this->meeting));
        $response->assertStatus(200);
        $response->assertSee('Form Builder:');
        $response->assertSee('Tambah Pertanyaan Baru');
    }

    public function test_admin_can_add_custom_question_field(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pertemuan.builder.store', $this->meeting), [
            'label' => 'Nomor WhatsApp Aktif',
            'type' => 'text',
            'description' => 'Untuk konfirmasi sertifikat',
            'placeholder' => '08xxxxxxxxxx',
            'is_required' => '1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_form_fields', [
            'meeting_id' => $this->meeting->id,
            'label' => 'Nomor WhatsApp Aktif',
            'type' => 'text',
            'is_required' => 1,
        ]);
    }

    public function test_public_attendance_submits_with_custom_answers(): void
    {
        Storage::fake('local');

        $field = MeetingFormField::create([
            'meeting_id' => $this->meeting->id,
            'field_key' => 'nomor_wa',
            'label' => 'Nomor WhatsApp',
            'type' => 'text',
            'is_required' => true,
            'order_index' => 1,
            'is_active' => true,
        ]);

        // Generate a small dummy PNG signature
        $im = imagecreatetruecolor(200, 100);
        ob_start();
        imagepng($im);
        $sigRaw = ob_get_clean();
        imagedestroy($im);
        $sigBase64 = 'data:image/png;base64,' . base64_encode($sigRaw);

        $response = $this->post(route('absensi.submit', $this->meeting->public_token), [
            'name' => 'Dr. Bambang Sutrisno',
            'nip' => '197508102000031001',
            'unit_kerja' => 'Industri Mitra Astra',
            'kabupaten_kota' => 'Kabupaten Majalengka',
            'signature' => $sigBase64,
            'custom_fields' => [
                'nomor_wa' => '081234567890',
            ],
        ]);

        $response->assertRedirect(route('absensi.success', $this->meeting->public_token));

        $this->assertDatabaseHas('attendances', [
            'meeting_id' => $this->meeting->id,
            'nip' => '197508102000031001',
            'name' => 'Dr. Bambang Sutrisno',
        ]);

        $this->assertDatabaseHas('attendance_custom_answers', [
            'field_key' => 'nomor_wa',
            'answer_value' => '081234567890',
        ]);
    }

    public function test_admin_can_apply_form_template(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.pertemuan.builder.template', $this->meeting), [
            'template_key' => 'rapat_komite',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('meeting_form_fields', [
            'meeting_id' => $this->meeting->id,
            'label' => 'Nama Peserta Didik / Anak',
        ]);
        $this->assertDatabaseHas('meeting_form_fields', [
            'meeting_id' => $this->meeting->id,
            'label' => 'Kelas & Konsentrasi Keahlian',
        ]);
    }

    public function test_meeting_duplicate_copies_custom_form_fields(): void
    {
        MeetingFormField::create([
            'meeting_id' => $this->meeting->id,
            'field_key' => 'saran_masukan',
            'label' => 'Saran & Masukan',
            'type' => 'textarea',
            'is_required' => false,
            'order_index' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.pertemuan.duplicate', $this->meeting));
        $response->assertRedirect();

        $duplicated = Meeting::where('title', 'like', '%(Salinan)%')->first();
        $this->assertNotNull($duplicated);

        $this->assertDatabaseHas('meeting_form_fields', [
            'meeting_id' => $duplicated->id,
            'field_key' => 'saran_masukan',
            'label' => 'Saran & Masukan',
        ]);
    }
}
