<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\Participant;
use App\Services\ImageOptimizer;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Throwable;

class PublicAttendanceController extends Controller
{
    /**
     * Show the public attendance form for the meeting.
     */
    public function show(Request $request, string $token): View|RedirectResponse|Response
    {
        $meeting = Meeting::where('public_token', $token)->firstOrFail();

        // 1. Kegiatan draf/arsip tidak boleh diakses publik.
        if (in_array($meeting->status, ['draft', 'archived'], true)) {
            return response()->view('public.attendance-closed', [
                'meeting' => $meeting,
                'title' => 'Kegiatan Belum Dapat Diakses',
                'message' => $meeting->status === 'draft'
                    ? 'Kegiatan ini masih berstatus draf dan belum dibuka untuk publik. Silakan hubungi panitia penyelenggara.'
                    : 'Kegiatan ini telah diarsipkan sehingga formulir kehadirannya tidak lagi dapat diakses.',
            ], 403);
        }

        // 2. Cek Status Publikasi Formulir
        if ($meeting->form_status === 'draft') {
            return response()->view('public.attendance-closed', [
                'meeting' => $meeting,
                'title' => 'Formulir Belum Dibuka',
                'message' => 'Formulir absensi untuk kegiatan ini belum dibuka oleh panitia. Silakan tunggu informasi pembukaan absensi di lokasi kegiatan.',
            ], 403);
        }

        if ($meeting->form_status === 'closed') {
            return response()->view('public.attendance-closed', [
                'meeting' => $meeting,
                'title' => 'Formulir Telah Ditutup',
                'message' => 'Pengisian absensi untuk kegiatan ini telah ditutup oleh panitia. Pengiriman kehadiran baru tidak dapat diterima.',
            ], 403);
        }

        // 3. Cek Mode Otorisasi (Jika Wajib Login NIP & Belum Login)
        $participant = Auth::guard('participant')->user();

        // Akun yang dinonaktifkan setelah login tidak boleh melanjutkan sesi.
        if ($participant && ! $participant->is_active) {
            Auth::guard('participant')->logout();
            $participant = null;
        }

        if (! $meeting->allow_anonymous && ! $participant) {
            return redirect()->route('absensi.meeting.login', $meeting->public_token)
                ->with('info', 'Kegiatan ini mewajibkan otentikasi NIP. Silakan masuk terlebih dahulu.');
        }

        // 4. Cek apakah NIP peserta sudah pernah absen sebelumnya.
        // Hanya memakai identitas dari sesi terautentikasi: parameter ?nip= publik
        // dapat dipakai untuk menebak status kehadiran orang lain.
        $existingNip = $participant?->nip;
        if ($existingNip) {
            $existing = Attendance::where('meeting_id', $meeting->id)
                ->where('nip', $existingNip)
                ->first();

            if ($existing) {
                return redirect()->route('absensi.success', $meeting->public_token)
                    ->with('info', 'Anda telah tercatat mengisi absensi untuk pertemuan ini pada pukul ' . substr((string) $existing->attended_time, 0, 5) . ' WIB.');
            }
        }

        // 5. Cek Batas Kuota Peserta (max_participants)
        if ($meeting->max_participants > 0) {
            $currentValidCount = Attendance::where('meeting_id', $meeting->id)
                ->where('status', 'valid')
                ->count();

            if ($currentValidCount >= $meeting->max_participants) {
                return response()->view('public.attendance-closed', [
                    'meeting' => $meeting,
                    'title' => 'Batas Kuota Peserta Terpenuhi',
                    'message' => "Batas kuota peserta untuk kegiatan ini ({$meeting->max_participants} orang) telah terpenuhi. Formulir tidak lagi menerima pengisian baru.",
                ], 403);
            }
        }

        // Pre-fill data peserta jika terotentikasi
        $prefilled = [
            'nip' => $participant->nip ?? '',
            'name' => $participant->name ?? '',
            'unit_kerja' => $participant->unit_kerja ?? 'SMKN 1 Majalengka',
            'kabupaten_kota' => $participant->kabupaten_kota ?? 'Kabupaten Majalengka',
        ];

        // Ambil field pertanyaan kustom tambahan
        $customFields = $meeting->activeFormFields;

        return view('public.attendance-form', compact('meeting', 'prefilled', 'participant', 'customFields'));
    }

    /**
     * Process and store the submitted attendance.
     */
    public function submit(StoreAttendanceRequest $request, string $token): RedirectResponse
    {
        $meeting = Meeting::where('public_token', $token)->firstOrFail();

        // 1. Validasi Status Kegiatan & Formulir
        if (in_array($meeting->status, ['draft', 'archived'], true)) {
            return redirect()->route('absensi.form', $token)
                ->with('error', 'Kegiatan ini belum dibuka untuk publik atau telah diarsipkan.');
        }

        if ($meeting->form_status !== 'published') {
            return redirect()->route('absensi.form', $token)
                ->with('error', 'Formulir absensi sedang tidak aktif atau telah ditutup.');
        }

        // 2. Otorisasi Peserta untuk Kegiatan Terbatas (bukan open attendance)
        $participant = Auth::guard('participant')->user();

        if ($participant && ! $participant->is_active) {
            Auth::guard('participant')->logout();
            $participant = null;
        }

        $nip = trim((string) $request->input('nip'));

        if (! $meeting->allow_anonymous) {
            if (! $participant) {
                return redirect()->route('absensi.meeting.login', $token)
                    ->with('error', 'Kegiatan ini mewajibkan otentikasi NIP. Silakan masuk terlebih dahulu.');
            }

            if ($participant->nip !== $nip) {
                return $this->backWithInputSafe($request)
                    ->with('error', 'NIP yang Anda masukkan tidak sesuai dengan akun yang sedang masuk.');
            }
        }

        // 3. Proteksi Anti-Duplikasi (Pencegahan Kehadiran Ganda)
        $alreadySubmitted = Attendance::where('meeting_id', $meeting->id)
            ->where('nip', $nip)
            ->first();

        if ($alreadySubmitted) {
            return $this->backWithInputSafe($request)->with('error', 'NIP tersebut telah tercatat hadir pada pertemuan ini. Pengisian kehadiran ganda tidak diperkenankan.');
        }

        // 4. Batas Kuota Peserta (max_participants)
        if ($meeting->max_participants > 0) {
            $currentValidCount = Attendance::where('meeting_id', $meeting->id)
                ->where('status', 'valid')
                ->count();

            if ($currentValidCount >= $meeting->max_participants) {
                return $this->backWithInputSafe($request)->with('error', "Batas kuota peserta untuk kegiatan ini ({$meeting->max_participants} orang) telah terpenuhi. Kehadiran baru tidak dapat diterima.");
            }
        }

        // 5. Validasi jawaban pertanyaan kustom SEBELUM berkas apa pun disimpan.
        $activeCustomFields = $meeting->activeFormFields;
        $customValues = $request->input('custom_fields', []);
        $customValues = is_array($customValues) ? $customValues : [];
        $customFiles = $request->file('custom_files', []);
        $customFiles = is_array($customFiles) ? $customFiles : [];

        if ($error = $this->validateCustomFieldResponses($activeCustomFields, $customValues, $customFiles)) {
            return $this->backWithInputSafe($request)->with('error', $error);
        }

        // 6. Simpan Gambar Tanda Tangan ke Private Storage dengan Validasi Ketat
        $signatureBase64 = (string) $request->input('signature');
        $imageParts = explode(';base64,', $signatureBase64);
        if (count($imageParts) < 2 || empty($imageParts[1])) {
            return $this->backWithInputSafe($request)->with('error', 'Format tanda tangan digital tidak valid.');
        }

        $imageBase64 = base64_decode($imageParts[1], true);
        if ($imageBase64 === false || strlen($imageBase64) < 50) {
            return $this->backWithInputSafe($request)->with('error', 'Tanda tangan digital kosong atau tidak terbaca. Silakan bubuhkan tanda tangan kembali.');
        }

        // Validasi Magic Bytes Header PNG (\x89PNG\r\n\x1a\n)
        if (substr($imageBase64, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return $this->backWithInputSafe($request)->with('error', 'Berkas tanda tangan digital harus berupa gambar PNG yang valid.');
        }

        // Kompresi dan Optimasi Otomatis Tanda Tangan Digital (Max 600x300, Level 9 Lossless PNG)
        $optimizedImage = ImageOptimizer::compressSignature($imageBase64, 600, 300);

        // Nama berkas acak: jangan menyimpan NIP (data pribadi) di jalur penyimpanan.
        $signatureFilename = 'sig_' . Str::random(32) . '.png';
        $signaturePath = 'signatures/' . $meeting->id . '/' . $signatureFilename;

        // 7. Hubungkan ke data Participant jika ada
        $participantRecord = Participant::where('nip', $nip)->first();

        $storedPaths = [$signaturePath];
        $attendance = null;
        Storage::disk('local')->put($signaturePath, $optimizedImage);

        try {
            DB::transaction(function () use ($meeting, $participantRecord, $request, $nip, $signaturePath, $activeCustomFields, $customValues, $customFiles, &$storedPaths, &$attendance) {
                // Waktu murni server Asia/Jakarta
                $attendance = Attendance::create([
                    'meeting_id' => $meeting->id,
                    'participant_id' => $participantRecord?->id,
                    'nip' => $nip,
                    'name' => $request->input('name'),
                    'unit_kerja' => $request->input('unit_kerja'),
                    'kabupaten_kota' => $request->input('kabupaten_kota'),
                    'signature_path' => $signaturePath,
                    'attended_date' => now()->toDateString(),
                    'attended_time' => now()->toTimeString(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'valid',
                ]);

                // Simpan Jawaban Field Kustom
                foreach ($activeCustomFields as $field) {
                    $answerText = null;
                    $filePath = null;

                    if ($field->type === 'file' && isset($customFiles[$field->field_key])) {
                        $uploadedFile = $customFiles[$field->field_key];
                        $mime = (string) $uploadedFile->getMimeType();

                        // Jika berupa gambar/foto, kompresi otomatis dengan ImageOptimizer
                        if (str_starts_with($mime, 'image/')) {
                            $compressedData = ImageOptimizer::compressPhoto($uploadedFile, 1280, 82);
                            $filename = 'custom_' . Str::slug($field->field_key) . '_' . Str::random(12) . '.jpg';
                            $destPath = 'attachments/' . $meeting->id . '/' . $filename;
                            Storage::disk('local')->put($destPath, $compressedData);
                            $filePath = $destPath;
                        } else {
                            // Dokumen biasa (PDF/Doc) simpan langsung
                            $filePath = $uploadedFile->store('attachments/' . $meeting->id, 'local');
                        }

                        if ($filePath) {
                            $storedPaths[] = $filePath;
                        }

                        $answerText = $uploadedFile->getClientOriginalName();
                    } elseif ($field->type === 'signature' && isset($customValues[$field->field_key])) {
                        $sigVal = (string) $customValues[$field->field_key];
                        if (str_contains($sigVal, ';base64,')) {
                            $parts = explode(';base64,', $sigVal);
                            $rawSig = base64_decode($parts[1] ?? '', true);
                            if ($rawSig && strlen($rawSig) >= 50 && substr($rawSig, 0, 8) === "\x89PNG\r\n\x1a\n") {
                                $optSig = ImageOptimizer::compressSignature($rawSig, 600, 300);
                                $filename = 'sig_' . Str::random(32) . '.png';
                                $destPath = 'signatures/' . $meeting->id . '/' . $filename;
                                Storage::disk('local')->put($destPath, $optSig);
                                $storedPaths[] = $destPath;
                                $filePath = $destPath;
                                $answerText = 'Tanda Tangan Digital Terverifikasi';
                            }
                        }
                    } elseif (isset($customValues[$field->field_key])) {
                        $val = $customValues[$field->field_key];
                        $answerText = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) $val;
                    }

                    if ($answerText !== null || $filePath !== null) {
                        $attendance->customAnswers()->create([
                            'meeting_form_field_id' => $field->id,
                            'field_key' => $field->field_key,
                            'field_label' => $field->label,
                            'answer_value' => $answerText,
                            'file_path' => $filePath,
                        ]);
                    }
                }

                // Jika peserta terdaftar, perbarui status undangan pertemuan
                if ($participantRecord) {
                    DB::table('meeting_participants')->updateOrInsert(
                        ['meeting_id' => $meeting->id, 'participant_id' => $participantRecord->id],
                        ['status' => 'attended', 'updated_at' => now()]
                    );
                }
            });
        } catch (Throwable $e) {
            // Jangan tinggalkan berkas yatim bila penyimpanan gagal (mis. balapan request ganda).
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            if ($this->isDuplicateKeyError($e)) {
                return $this->backWithInputSafe($request)->with('error', 'NIP tersebut telah tercatat hadir pada pertemuan ini. Pengisian kehadiran ganda tidak diperkenankan.');
            }

            report($e);

            return $this->backWithInputSafe($request)->with('error', 'Kehadiran gagal disimpan karena kendala teknis. Silakan coba lagi atau hubungi panitia.');
        }

        session([
            'last_attendance_token' => $meeting->public_token,
            'last_attendance_id' => $attendance->id,
            'last_attendance_name' => $attendance->name,
            'last_attendance_time' => substr((string) $attendance->attended_time, 0, 5),
        ]);

        return redirect()->route('absensi.success', $meeting->public_token)
            ->with('success', 'Kehadiran Anda telah berhasil dicatat oleh sistem.');
    }

    /**
     * Display confirmation screen upon successful attendance submission.
     */
    public function success(Request $request, string $token): View
    {
        $meeting = Meeting::where('public_token', $token)->firstOrFail();

        $participant = Auth::guard('participant')->user();

        // Data sesi hanya ditampilkan bila memang berasal dari pertemuan yang sama,
        // agar konfirmasi tidak menampilkan nama dari kegiatan lain.
        $sessionMatches = session('last_attendance_token') === $meeting->public_token;

        $attendanceInfo = [
            'name' => $sessionMatches
                ? session('last_attendance_name', $participant?->name ?? 'Peserta')
                : ($participant?->name ?? 'Peserta'),
            'time' => $sessionMatches
                ? session('last_attendance_time', now()->format('H:i'))
                : now()->format('H:i'),
        ];

        return view('public.attendance-success', compact('meeting', 'attendanceInfo'));
    }

    /**
     * Validate required custom field responses and option membership before persisting anything.
     *
     * @param  Collection<int, \App\Models\MeetingFormField>  $fields
     * @param  array<string, mixed>  $values
     * @param  array<string, mixed>  $files
     */
    private function validateCustomFieldResponses(Collection $fields, array $values, array $files): ?string
    {
        foreach ($fields as $field) {
            $key = $field->field_key;
            $raw = $values[$key] ?? null;

            if ($field->type === 'file') {
                if ($field->is_required && empty($files[$key])) {
                    return "Kolom \"{$field->label}\" wajib diunggah.";
                }

                continue;
            }

            if ($field->type === 'signature') {
                if ($field->is_required && (! is_string($raw) || ! str_starts_with($raw, 'data:image/png;base64,'))) {
                    return "Tanda tangan pada kolom \"{$field->label}\" wajib dibubuhkan.";
                }

                continue;
            }

            if (in_array($field->type, ['select', 'radio', 'checkbox', 'scale'], true)) {
                // Skala linier tanpa opsi tersimpan memakai tingkatan bawaan 1-5.
                $options = $field->options ?? ($field->type === 'scale' ? ['1', '2', '3', '4', '5'] : []);
                $selected = is_array($raw) ? $raw : ($raw === null || $raw === '' ? [] : [$raw]);

                if ($field->is_required && $selected === []) {
                    return "Kolom \"{$field->label}\" wajib diisi.";
                }

                foreach ($selected as $value) {
                    if (! is_scalar($value) || ! in_array((string) $value, $options, true)) {
                        return "Pilihan pada kolom \"{$field->label}\" tidak valid.";
                    }
                }

                continue;
            }

            if ($field->type === 'email') {
                if ($raw !== null && $raw !== '' && ! filter_var($raw, FILTER_VALIDATE_EMAIL)) {
                    return "Format email pada kolom \"{$field->label}\" tidak valid.";
                }
            }

            if ($field->is_required && ($raw === null || $raw === '' || (is_array($raw) && $raw === []))) {
                return "Kolom \"{$field->label}\" wajib diisi.";
            }
        }

        return null;
    }

    /**
     * Redirect back with old input, excluding large base64 payloads that cannot be re-rendered.
     */
    private function backWithInputSafe(Request $request): RedirectResponse
    {
        $values = $request->except(['signature', 'custom_files', 'custom_fields']);

        $custom = $request->input('custom_fields', []);
        if (is_array($custom)) {
            $values['custom_fields'] = collect($custom)
                ->reject(fn ($value) => is_string($value) && str_starts_with($value, 'data:'))
                ->all();
        }

        return back()->withInput($values);
    }

    /**
     * Detect a unique constraint violation across SQLite, MySQL and PostgreSQL.
     */
    private function isDuplicateKeyError(Throwable $e): bool
    {
        if (! $e instanceof QueryException) {
            return false;
        }

        if (($e->errorInfo[1] ?? null) === 1062) {
            return true;
        }

        $message = strtoupper($e->getMessage());

        return str_contains($message, 'UNIQUE CONSTRAINT')
            || str_contains($message, 'DUPLICATE ENTRY')
            || str_contains($message, 'SQLSTATE[23505]');
    }
}
