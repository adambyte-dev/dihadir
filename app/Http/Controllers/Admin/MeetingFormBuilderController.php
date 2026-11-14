<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\MeetingFormField;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MeetingFormBuilderController extends Controller
{
    /**
     * Display the Google Forms-like form builder interface.
     */
    public function index(Request $request, Meeting $meeting): View
    {
        $this->authorize('update', $meeting);

        $meeting->load('formFields');

        $templates = self::getPredefinedTemplates();

        return view('admin.meetings.builder', compact('meeting', 'templates'));
    }

    /**
     * Apply a predefined template to the meeting form builder.
     */
    public function applyTemplate(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $request->validate([
            'template_key' => ['required', 'string', 'max:50'],
        ]);

        $templates = self::getPredefinedTemplates();
        $templateKey = $request->input('template_key');

        if (!isset($templates[$templateKey])) {
            return back()->with('error', 'Template formulir tidak dikenali.');
        }

        $template = $templates[$templateKey];
        $currentMax = (int) $meeting->formFields()->max('order_index');

        foreach ($template['fields'] as $index => $fieldData) {
            $baseSlug = Str::slug($fieldData['label'], '_');
            $fieldKey = $baseSlug ?: 'field_' . Str::lower(Str::random(6));

            if ($meeting->formFields()->where('field_key', $fieldKey)->exists()) {
                $fieldKey = $fieldKey . '_' . Str::lower(Str::random(4));
            }

            $meeting->formFields()->create([
                'field_key' => $fieldKey,
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'description' => $fieldData['description'] ?? null,
                'placeholder' => $fieldData['placeholder'] ?? null,
                'is_required' => $fieldData['is_required'] ?? false,
                'options' => $fieldData['options'] ?? null,
                'order_index' => $currentMax + $index + 1,
                'is_active' => true,
            ]);
        }

        AuditLogger::log('form_template_applied', $meeting, [
            'meeting_id' => $meeting->id,
            'template_key' => $templateKey,
            'template_title' => $template['title'],
        ]);

        return back()->with('success', "Template \"{$template['title']}\" berhasil diterapkan ke formulir.");
    }

    /**
     * Predefined Form Templates for SMKN 1 Majalengka.
     */
    public static function getPredefinedTemplates(): array
    {
        return [
            'rapat_komite' => [
                'title' => 'Rapat Pleno Komite & Orang Tua Siswa',
                'description' => 'Paket pertanyaan pendataan orang tua murid (nama anak, rombel kelas, hubungan keluarga, dan nomor kontak).',
                'fields' => [
                    [
                        'label' => 'Nama Peserta Didik / Anak',
                        'type' => 'text',
                        'description' => 'Nama lengkap putra/putri yang bersekolah di SMKN 1 Majalengka',
                        'placeholder' => 'Contoh: Ahmad Rizky',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Kelas & Konsentrasi Keahlian',
                        'type' => 'select',
                        'description' => 'Pilih kelas anak Anda saat ini',
                        'placeholder' => null,
                        'is_required' => true,
                        'options' => [
                            'X - Rekayasa Perangkat Lunak',
                            'X - Teknik Komputer & Jaringan',
                            'X - Teknik Kendaraan Ringan',
                            'X - Teknik Pemesinan',
                            'XI - Rekayasa Perangkat Lunak',
                            'XI - Teknik Komputer & Jaringan',
                            'XI - Teknik Kendaraan Ringan',
                            'XI - Teknik Pemesinan',
                            'XII - Rekayasa Perangkat Lunak',
                            'XII - Teknik Komputer & Jaringan',
                            'XII - Teknik Kendaraan Ringan',
                            'XII - Teknik Pemesinan',
                        ],
                    ],
                    [
                        'label' => 'Hubungan Keluarga dengan Siswa',
                        'type' => 'radio',
                        'description' => 'Status kehadiran sebagai perwakilan siswa',
                        'is_required' => true,
                        'options' => ['Ayah Kandung', 'Ibu Kandung', 'Wali / Saudara'],
                    ],
                    [
                        'label' => 'Nomor WhatsApp Aktif',
                        'type' => 'text',
                        'description' => 'Untuk koordinasi hasil rapat komite',
                        'placeholder' => '08xxxxxxxxxx',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Saran atau Masukan untuk Komite Sekolah',
                        'type' => 'textarea',
                        'description' => 'Aspirasi orang tua untuk kemajuan SMKN 1 Majalengka',
                        'placeholder' => 'Tuliskan masukan Anda...',
                        'is_required' => false,
                    ],
                ],
            ],
            'workshop_guru' => [
                'title' => 'Workshop & Pelatihan Internal Guru / PTK',
                'description' => 'Khusus kegiatan IHT, pembinaan dinas, dan peningkatan kompetensi guru/pegawai.',
                'fields' => [
                    [
                        'label' => 'Mata Pelajaran yang Diampu',
                        'type' => 'text',
                        'description' => 'Mata pelajaran / kejuruan guru yang bersangkutan',
                        'placeholder' => 'Contoh: Kejuruan RPL / Matematika',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Status Kepegawaian',
                        'type' => 'radio',
                        'description' => 'Status kepegawaian di SMKN 1 Majalengka',
                        'is_required' => true,
                        'options' => ['PNS', 'PPPK', 'Guru Tetap Yayasan', 'Guru Honorer Sekolah'],
                    ],
                    [
                        'label' => 'Unggah Bukti Surat Tugas / Foto Kegiatan',
                        'type' => 'file',
                        'description' => 'Unggah dokumen penugasan atau foto bukti keikutsertaan (Otomatis dikompresi)',
                        'is_required' => false,
                    ],
                ],
            ],
            'kunjungan_dudi' => [
                'title' => 'Kunjungan Industri & Kemitraan DUDI',
                'description' => 'Untuk sinkronisasi kurikulum, MoU kerja sama industri, dan koordinasi magang PKL.',
                'fields' => [
                    [
                        'label' => 'Nama Perusahaan / Asosiasi Industri',
                        'type' => 'text',
                        'description' => 'Nama lengkap entitas bisnis atau industri mitra',
                        'placeholder' => 'Contoh: PT Surya Majalengka Mandiri',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Jabatan / Posisi dalam Perusahaan',
                        'type' => 'text',
                        'description' => 'Peran perwakilan industri yang hadir',
                        'placeholder' => 'Contoh: HR Director / Technical Lead',
                        'is_required' => true,
                    ],
                    [
                        'label' => 'Kesediaan Menerima Peserta PKL SMKN 1 Majalengka',
                        'type' => 'radio',
                        'description' => 'Komitmen penerimaan siswa magang periode mendatang',
                        'is_required' => true,
                        'options' => ['Bersedia', 'Perlu Pembahasan Lanjutan', 'Belum Memungkinkan'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Store a newly created custom question field.
     */
    public function storeField(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,email,phone,select,radio,checkbox,file,signature,date,time,scale',
            'description' => 'nullable|string|max:500',
            'placeholder' => 'nullable|string|max:150',
            'is_required' => 'nullable|boolean',
            'options_raw' => 'nullable|string', // Baris baru dipisah per opsi
        ]);

        $options = $this->parseOptions($request->input('type'), $request->input('options_raw'));

        $nextOrder = (int) $meeting->formFields()->max('order_index') + 1;
        $baseSlug = Str::slug($request->label, '_');
        $fieldKey = $baseSlug ?: 'field_' . Str::lower(Str::random(6));

        // Hindari duplikasi field_key pada meeting yang sama
        if ($meeting->formFields()->where('field_key', $fieldKey)->exists()) {
            $fieldKey = $fieldKey . '_' . Str::lower(Str::random(4));
        }

        $field = $meeting->formFields()->create([
            'field_key' => $fieldKey,
            'label' => $request->label,
            'type' => $request->type,
            'description' => $request->description,
            'placeholder' => $request->placeholder,
            'is_required' => $request->boolean('is_required'),
            'options' => $options,
            'order_index' => $nextOrder,
            'is_active' => true,
        ]);

        AuditLogger::log('form_field_created', $meeting, [
            'meeting_id' => $meeting->id,
            'field_key' => $field->field_key,
            'label' => $field->label,
            'type' => $field->type,
        ]);

        return back()->with('success', "Pertanyaan \"{$field->label}\" berhasil ditambahkan ke formulir.");
    }

    /**
     * Update an existing custom question field.
     */
    public function updateField(Request $request, Meeting $meeting, MeetingFormField $field): RedirectResponse
    {
        $this->authorize('update', $meeting);

        if ($field->meeting_id !== $meeting->id) {
            abort(404);
        }

        $request->validate([
            'label' => 'required|string|max:255',
            'type' => 'required|in:text,textarea,number,email,phone,select,radio,checkbox,file,signature,date,time,scale',
            'description' => 'nullable|string|max:500',
            'placeholder' => 'nullable|string|max:150',
            'is_required' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'options_raw' => 'nullable|string',
        ]);

        $options = $this->parseOptions($request->input('type'), $request->input('options_raw'));

        $field->update([
            'label' => $request->label,
            'type' => $request->type,
            'description' => $request->description,
            'placeholder' => $request->placeholder,
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->has('is_active') ? $request->boolean('is_active') : $field->is_active,
            'options' => $options,
        ]);

        AuditLogger::log('form_field_updated', $meeting, [
            'meeting_id' => $meeting->id,
            'field_key' => $field->field_key,
            'label' => $field->label,
        ]);

        return back()->with('success', "Pertanyaan \"{$field->label}\" berhasil diperbarui.");
    }

    /**
     * Toggle status aktif/nonaktif sebuah pertanyaan formulir.
     */
    public function toggleActive(Request $request, Meeting $meeting, MeetingFormField $field): RedirectResponse
    {
        $this->authorize('update', $meeting);

        if ($field->meeting_id !== $meeting->id) {
            abort(404);
        }

        $field->update([
            'is_active' => ! $field->is_active,
        ]);

        $statusText = $field->is_active ? 'diaktifkan' : 'dinonaktifkan (disembunyikan dari peserta)';

        AuditLogger::log('form_field_toggled', $meeting, [
            'meeting_id' => $meeting->id,
            'field_key' => $field->field_key,
            'is_active' => $field->is_active,
        ]);

        return back()->with('success', "Pertanyaan \"{$field->label}\" berhasil {$statusText}.");
    }

    /**
     * Delete a custom question field.
     */
    public function destroyField(Request $request, Meeting $meeting, MeetingFormField $field): RedirectResponse
    {
        $this->authorize('update', $meeting);

        if ($field->meeting_id !== $meeting->id) {
            abort(404);
        }

        // Pertanyaan yang sudah dijawab peserta tidak boleh dihapus agar data
        // kehadiran historis tetap utuh dan dapat dilaporkan kembali.
        if ($field->answers()->exists()) {
            return back()->with('error', 'Pertanyaan "' . $field->label . '" sudah memiliki jawaban peserta sehingga tidak dapat dihapus. Nonaktifkan pertanyaan ini bila tidak ingin digunakan lagi.');
        }

        $label = $field->label;
        $fieldKey = $field->field_key;
        $field->delete();

        AuditLogger::log('form_field_deleted', $meeting, [
            'meeting_id' => $meeting->id,
            'field_key' => $fieldKey,
            'label' => $label,
        ]);

        return back()->with('success', "Pertanyaan \"{$label}\" telah dihapus dari formulir.");
    }

    /**
     * Ubah baris opsi menjadi daftar opsi tervalidasi.
     * Field pilihan (select/radio/checkbox) wajib memiliki minimal satu opsi
     * agar formulir publik tidak pernah menampilkan pilihan kosong.
     *
     * @return list<string>|null
     *
     * @throws ValidationException
     */
    private function parseOptions(?string $type, ?string $raw): ?array
    {
        if ($type === 'scale') {
            // Default scale jika tidak diisi opsi khusus: 1 sampai 5
            $lines = preg_split('/\r\n|\r|\n/', (string) $raw) ?: [];
            $options = array_values(array_unique(array_filter(array_map('trim', $lines), fn ($value) => $value !== '')));
            return $options !== [] ? $options : ['1 - Sangat Kurang', '2 - Kurang', '3 - Cukup', '4 - Baik', '5 - Sangat Baik'];
        }

        if (! in_array($type, ['select', 'radio', 'checkbox'], true)) {
            return null;
        }

        $lines = preg_split('/\r\n|\r|\n/', (string) $raw) ?: [];
        $options = array_values(array_unique(array_filter(array_map('trim', $lines), fn ($value) => $value !== '')));

        if ($options === []) {
            throw ValidationException::withMessages([
                'options_raw' => 'Opsi pilihan wajib diisi minimal satu untuk tipe pertanyaan pilihan.',
            ]);
        }

        if (count($options) > 50) {
            throw ValidationException::withMessages([
                'options_raw' => 'Jumlah opsi pilihan maksimal 50 baris.',
            ]);
        }

        foreach ($options as $option) {
            if (mb_strlen($option) > 150) {
                throw ValidationException::withMessages([
                    'options_raw' => 'Setiap opsi pilihan maksimal 150 karakter.',
                ]);
            }
        }

        return $options;
    }

    /**
     * Reorder fields via up/down buttons.
     */
    public function reorder(Request $request, Meeting $meeting, MeetingFormField $field, string $direction): RedirectResponse
    {
        $this->authorize('update', $meeting);

        // Pastikan field benar-benar milik pertemuan pada URL (mencegah mutasi lintas pertemuan).
        if ($field->meeting_id !== $meeting->id) {
            abort(404);
        }

        $currentOrder = $field->order_index;
        $neighbor = null;

        if ($direction === 'up') {
            $neighbor = $meeting->formFields()
                ->where('order_index', '<', $currentOrder)
                ->orderBy('order_index', 'desc')
                ->first();
        } elseif ($direction === 'down') {
            $neighbor = $meeting->formFields()
                ->where('order_index', '>', $currentOrder)
                ->orderBy('order_index', 'asc')
                ->first();
        }

        if ($neighbor) {
            $neighborOrder = $neighbor->order_index;
            $field->update(['order_index' => $neighborOrder]);
            $neighbor->update(['order_index' => $currentOrder]);
        }

        return back()->with('success', 'Urutan pertanyaan berhasil disesuaikan.');
    }
}
