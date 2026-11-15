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

        // 4. Cek apakah NIP peserta sudah pernah absen sebelumnya (jika terotentikasi).
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

        // Ambil seluruh pertanyaan aktif yang disusun admin di Form Builder
        $formFields = $meeting->activeFormFields;

        // Prefill values jika peserta sudah terotentikasi
        $prefilled = [
            'nip' => $participant->nip ?? '',
            'name' => $participant->name ?? '',
            'unit_kerja' => $participant->unit_kerja ?? 'SMKN 1 Majalengka',
            'kabupaten_kota' => $participant->kabupaten_kota ?? 'Kabupaten Majalengka',
        ];

        return view('public.attendance-form', compact('meeting', 'participant', 'formFields', 'prefilled'));
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

        if (! $meeting->allow_anonymous && ! $participant) {
            return redirect()->route('absensi.meeting.login', $token)
                ->with('error', 'Kegiatan ini mewajibkan otentikasi NIP. Silakan masuk terlebih dahulu.');
        }

        // 3. Ambil data pertanyaan aktif di formulir pertemuan ini
        $activeFields = $meeting->activeFormFields;

        // Mendukung baik input terstruktur dinamis (fields[...]), custom_fields[...], maupun input formulir langsung (name, nip, ...)
        $inputValues = $request->input('fields', []);
        $inputValues = is_array($inputValues) ? $inputValues : [];
        $customValues = $request->input('custom_fields', []);
        if (is_array($customValues)) {
            $inputValues = array_merge($customValues, $inputValues);
        }
        $inputFiles = $request->file('files', []);
        $inputFiles = is_array($inputFiles) ? $inputFiles : [];
        $customFiles = $request->file('custom_files', []);
        if (is_array($customFiles)) {
            $inputFiles = array_merge($customFiles, $inputFiles);
        }

        // Fallback kompatibilitas: jika ada input langsung di request, petakan ke field dinamis terkait
        $mappedDefaults = [
            'name' => ['nama_lengkap', 'nama', 'name'],
            'nip' => ['nomor_induk_pegawai', 'nip'],
            'unit_kerja' => ['unit_kerja', 'instansi'],
            'kabupaten_kota' => ['kabupaten_kota', 'kabupaten', 'kota'],
            'signature' => ['tanda_tangan', 'signature'],
        ];

        foreach ($mappedDefaults as $directKey => $aliases) {
            if ($request->filled($directKey)) {
                $val = $request->input($directKey);
                $inputValues[$directKey] = $val;

                // Petakan juga ke matching field_key pada activeFields
                foreach ($activeFields as $af) {
                    $k = strtolower($af->field_key);
                    foreach ($aliases as $alias) {
                        if (str_contains($k, $alias) && !isset($inputValues[$af->field_key])) {
                            $inputValues[$af->field_key] = $val;
                            break;
                        }
                    }
                }
            }
        }

        // Validasi seluruh jawaban pertanyaan yang wajib diisi jika form fields dikonfigurasi
        if ($activeFields->isNotEmpty()) {
            if ($error = $this->validateCustomFieldResponses($activeFields, $inputValues, $inputFiles)) {
                return $this->backWithInputSafe($request)->with('error', $error);
            }
        }

        // Cek apakah ada field NIP untuk proteksi anti-duplikasi
        $nipField = $activeFields->first(function ($f) {
            $lower = strtolower($f->label . ' ' . $f->field_key);
            return str_contains($lower, 'nip') || $f->field_key === 'nip';
        });

        $submittedNip = null;
        if ($nipField && isset($inputValues[$nipField->field_key])) {
            $submittedNip = trim((string) $inputValues[$nipField->field_key]);
        } elseif (isset($inputValues['nip'])) {
            $submittedNip = trim((string) $inputValues['nip']);
        } elseif ($participant?->nip) {
            $submittedNip = $participant->nip;
        }

        // Jika ada NIP terdeteksi, cegah pengisian ganda
        if ($submittedNip !== null && $submittedNip !== '') {
            $alreadySubmitted = Attendance::where('meeting_id', $meeting->id)
                ->where('nip', $submittedNip)
                ->first();

            if ($alreadySubmitted) {
                return $this->backWithInputSafe($request)->with('error', "NIP {$submittedNip} telah tercatat hadir pada pertemuan ini. Pengisian presensi ganda tidak diperkenankan.");
            }
        }

        // Batas Kuota Peserta (max_participants)
        if ($meeting->max_participants > 0) {
            $currentValidCount = Attendance::where('meeting_id', $meeting->id)
                ->where('status', 'valid')
                ->count();

            if ($currentValidCount >= $meeting->max_participants) {
                return $this->backWithInputSafe($request)->with('error', "Batas kuota peserta ({$meeting->max_participants} orang) telah terpenuhi.");
            }
        }

        // Cek field Nama untuk nama peserta di rekap/tabel
        $nameField = $activeFields->first(function ($f) {
            $lower = strtolower($f->label . ' ' . $f->field_key);
            return str_contains($lower, 'nama') || $f->field_key === 'name';
        });
        $submittedName = $nameField && isset($inputValues[$nameField->field_key])
            ? trim((string) $inputValues[$nameField->field_key])
            : (isset($inputValues['name']) ? trim((string) $inputValues['name']) : ($participant?->name ?? 'Peserta Presensi'));

        // Cek field Unit Kerja
        $unitField = $activeFields->first(function ($f) {
            $lower = strtolower($f->label . ' ' . $f->field_key);
            return str_contains($lower, 'unit') || str_contains($lower, 'instansi') || $f->field_key === 'unit_kerja';
        });
        $submittedUnit = $unitField && isset($inputValues[$unitField->field_key])
            ? trim((string) $inputValues[$unitField->field_key])
            : (isset($inputValues['unit_kerja']) ? trim((string) $inputValues['unit_kerja']) : ($participant?->unit_kerja ?? null));

        // Cek field Kabupaten / Kota
        $kabField = $activeFields->first(function ($f) {
            $lower = strtolower($f->label . ' ' . $f->field_key);
            return str_contains($lower, 'kabupaten') || str_contains($lower, 'kota') || $f->field_key === 'kabupaten_kota';
        });
        $submittedKab = $kabField && isset($inputValues[$kabField->field_key])
            ? trim((string) $inputValues[$kabField->field_key])
            : (isset($inputValues['kabupaten_kota']) ? trim((string) $inputValues['kabupaten_kota']) : ($participant?->kabupaten_kota ?? null));

        // Cari tanda tangan jika ada field bertipe signature atau direct signature
        $sigField = $activeFields->firstWhere('type', 'signature');
        $rawSig = null;
        if ($sigField && !empty($inputValues[$sigField->field_key])) {
            $rawSig = (string) $inputValues[$sigField->field_key];
        } elseif (!empty($inputValues['signature'])) {
            $rawSig = (string) $inputValues['signature'];
        }

        $signaturePath = null;
        $storedPaths = [];

        if ($rawSig && str_contains($rawSig, ';base64,')) {
            $parts = explode(';base64,', $rawSig);
            $decodedSig = base64_decode($parts[1] ?? '', true);
            if ($decodedSig && strlen($decodedSig) >= 50 && substr($decodedSig, 0, 8) === "\x89PNG\r\n\x1a\n") {
                $optSig = ImageOptimizer::compressSignature($decodedSig, 600, 300);
                $sigFilename = 'sig_' . Str::random(32) . '.png';
                $signaturePath = 'signatures/' . $meeting->id . '/' . $sigFilename;
                Storage::disk('local')->put($signaturePath, $optSig);
                $storedPaths[] = $signaturePath;
            }
        }

        $participantRecord = $submittedNip ? Participant::where('nip', $submittedNip)->first() : null;
        $attendance = null;

        try {
            DB::transaction(function () use ($meeting, $participantRecord, $request, $submittedNip, $submittedName, $submittedUnit, $submittedKab, $signaturePath, $activeFields, $inputValues, $inputFiles, &$storedPaths, &$attendance) {
                $attendance = Attendance::create([
                    'meeting_id' => $meeting->id,
                    'participant_id' => $participantRecord?->id,
                    'nip' => $submittedNip ?: ('PST-' . Str::upper(Str::random(8))),
                    'name' => $submittedName,
                    'unit_kerja' => $submittedUnit ?: '-',
                    'kabupaten_kota' => $submittedKab ?: '-',
                    'signature_path' => $signaturePath ?: '',
                    'attended_date' => now()->toDateString(),
                    'attended_time' => now()->toTimeString(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'status' => 'valid',
                ]);

                // Simpan Jawaban seluruh field dinamis
                foreach ($activeFields as $field) {
                    $answerText = null;
                    $filePath = null;

                    if ($field->type === 'file' && isset($inputFiles[$field->field_key])) {
                        $uploadedFile = $inputFiles[$field->field_key];
                        $mime = (string) $uploadedFile->getMimeType();

                        if (str_starts_with($mime, 'image/')) {
                            $compressedData = ImageOptimizer::compressPhoto($uploadedFile, 1280, 82);
                            $filename = 'custom_' . Str::slug($field->field_key) . '_' . Str::random(12) . '.jpg';
                            $destPath = 'attachments/' . $meeting->id . '/' . $filename;
                            Storage::disk('local')->put($destPath, $compressedData);
                            $filePath = $destPath;
                        } else {
                            $filePath = $uploadedFile->store('attachments/' . $meeting->id, 'local');
                        }

                        if ($filePath) {
                            $storedPaths[] = $filePath;
                        }

                        $answerText = $uploadedFile->getClientOriginalName();
                    } elseif ($field->type === 'signature') {
                        $filePath = $signaturePath;
                        $answerText = $signaturePath ? 'Tanda Tangan Digital Terverifikasi' : null;
                    } elseif (isset($inputValues[$field->field_key])) {
                        $val = $inputValues[$field->field_key];
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

                if ($participantRecord) {
                    DB::table('meeting_participants')->updateOrInsert(
                        ['meeting_id' => $meeting->id, 'participant_id' => $participantRecord->id],
                        ['status' => 'attended', 'updated_at' => now()]
                    );
                }
            });
        } catch (Throwable $e) {
            foreach ($storedPaths as $path) {
                Storage::disk('local')->delete($path);
            }

            if ($this->isDuplicateKeyError($e)) {
                return $this->backWithInputSafe($request)->with('error', 'NIP tersebut telah tercatat hadir pada pertemuan ini.');
            }

            report($e);
            return $this->backWithInputSafe($request)->with('error', 'Presensi gagal disimpan karena kendala teknis: ' . $e->getMessage());
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
     * Validate responses according to meeting active fields.
     */
    private function validateCustomFieldResponses(Collection $fields, array $values, array $files): ?string
    {
        foreach ($fields as $field) {
            $key = $field->field_key;
            $raw = $values[$key] ?? null;

            if ($field->type === 'file') {
                if ($field->is_required && empty($files[$key])) {
                    return "Pertanyaan \"{$field->label}\" wajib diunggah.";
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
                $options = $field->options ?? ($field->type === 'scale' ? ['1', '2', '3', '4', '5'] : []);
                $selected = is_array($raw) ? $raw : ($raw === null || $raw === '' ? [] : [$raw]);

                if ($field->is_required && $selected === []) {
                    return "Pertanyaan \"{$field->label}\" wajib diisi / dipilih.";
                }

                continue;
            }

            if ($field->type === 'email') {
                if ($raw !== null && $raw !== '' && ! filter_var($raw, FILTER_VALIDATE_EMAIL)) {
                    return "Format email pada pertanyaan \"{$field->label}\" tidak valid.";
                }
            }

            if ($field->is_required && ($raw === null || $raw === '' || (is_array($raw) && $raw === []))) {
                return "Pertanyaan \"{$field->label}\" wajib diisi.";
            }
        }

        return null;
    }

    /**
     * Redirect back with old input safe from base64.
     */
    private function backWithInputSafe(Request $request): RedirectResponse
    {
        $values = $request->except(['files']);
        $fields = $request->input('fields', []);

        if (is_array($fields)) {
            $values['fields'] = collect($fields)
                ->reject(fn ($value) => is_string($value) && str_starts_with($value, 'data:'))
                ->all();
        }

        return back()->withInput($values);
    }

    /**
     * Detect a unique constraint violation.
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
