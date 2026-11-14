<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Participant;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class ParticipantController extends Controller
{
    /**
     * Display a listing of participants.
     */
    public function index(Request $request): View
    {
        $query = Participant::latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kabupaten_kota')) {
            $query->where('kabupaten_kota', $request->input('kabupaten_kota'));
        }

        $participants = $query->paginate(15)->withQueryString();

        return view('admin.participants.index', compact('participants'));
    }

    /**
     * Show the form for creating a new participant.
     */
    public function create(): View
    {
        return view('admin.participants.create');
    }

    /**
     * Store a newly created participant in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:32', Rule::unique('participants', 'nip')->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'unit_kerja' => ['required', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:24'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'nip.unique' => 'NIP sudah terdaftar di sistem.',
            'name.required' => 'Nama lengkap peserta wajib diisi.',
            'unit_kerja.required' => 'Unit kerja wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        // Default password adalah NIP jika tidak ditentukan
        $password = !empty($validated['password']) ? $validated['password'] : $validated['nip'];
        $validated['password'] = Hash::make($password);
        $validated['is_active'] = $request->boolean('is_active', true);

        $participant = Participant::create($validated);

        AuditLogger::log('participant_created', $participant, [
            'nip' => $participant->nip,
            'name' => $participant->name,
        ]);

        return redirect()->route('admin.peserta.index')
            ->with('success', 'Data peserta "'.$validated['name'].'" berhasil ditambahkan.');
    }

    /**
     * Display the specified participant (redirect to edit).
     */
    public function show(Participant $participant): RedirectResponse
    {
        return redirect()->route('admin.peserta.edit', $participant);
    }

    /**
     * Show the form for editing the specified participant.
     */
    public function edit(Participant $participant): View
    {
        return view('admin.participants.edit', compact('participant'));
    }

    /**
     * Update the specified participant in storage.
     */
    public function update(Request $request, Participant $participant): RedirectResponse
    {
        $validated = $request->validate([
            'nip' => ['required', 'string', 'max:32', Rule::unique('participants', 'nip')->ignore($participant->id)->whereNull('deleted_at')],
            'name' => ['required', 'string', 'max:255'],
            'unit_kerja' => ['required', 'string', 'max:255'],
            'kabupaten_kota' => ['nullable', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:24'],
            'password' => ['nullable', 'string', 'min:8'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'nip.unique' => 'NIP sudah digunakan oleh peserta lain.',
            'name.required' => 'Nama peserta wajib diisi.',
            'unit_kerja.required' => 'Unit kerja wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $participant->update($validated);

        AuditLogger::log('participant_updated', $participant, [
            'nip' => $participant->nip,
            'name' => $participant->name,
        ]);

        return redirect()->route('admin.peserta.index')
            ->with('success', 'Data peserta "'.$participant->name.'" berhasil diperbarui.');
    }

    /**
     * Remove the specified participant from storage.
     */
    public function destroy(Participant $participant): RedirectResponse
    {
        $name = $participant->name;
        $nip = $participant->nip;

        AuditLogger::log('participant_deleted', $participant, [
            'nip' => $nip,
            'name' => $name,
        ]);

        $participant->delete();

        return redirect()->route('admin.peserta.index')
            ->with('success', 'Peserta "'.$name.'" berhasil dihapus.');
    }

    /**
     * Bulk import participants via CSV.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ], [
            'file.required' => 'Pilih berkas CSV terlebih dahulu.',
            'file.mimes' => 'Format berkas harus berupa CSV.',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->with('error', 'Berkas CSV tidak dapat dibaca. Pastikan berkas tidak rusak.');
        }

        $header = fgetcsv($handle, 0, ',', '"', '\\');
        $columns = $this->mapCsvColumns($header);

        $created = 0;
        $updated = 0;
        $skipped = 0;

        try {
            DB::transaction(function () use ($handle, $columns, &$created, &$updated, &$skipped) {
                while (($row = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
                    if (! is_array($row) || $row === [null]) {
                        continue;
                    }

                    $nip = trim((string) ($row[$columns['nip']] ?? ''));
                    $name = trim((string) ($row[$columns['name']] ?? ''));
                    $unitKerja = trim((string) ($row[$columns['unit_kerja']] ?? ''));

                    // Baris tidak lengkap atau melebihi batas kolom database dilewati, bukan menggagalkan seluruh impor.
                    if ($nip === '' || $name === '' || $unitKerja === '') {
                        $skipped++;

                        continue;
                    }

                    if (mb_strlen($nip) > 32 || mb_strlen($name) > 255 || mb_strlen($unitKerja) > 255) {
                        $skipped++;

                        continue;
                    }

                    $kabKota = trim((string) ($row[$columns['kabupaten_kota']] ?? ''));
                    $phone = trim((string) ($row[$columns['phone_number']] ?? ''));

                    // Sertakan data yang sudah soft-deleted agar NIP lama dapat dipulihkan
                    // tanpa melanggar unique index.
                    $participant = Participant::withTrashed()->firstOrNew(['nip' => $nip]);
                    $isNew = ! $participant->exists;

                    $participant->name = $name;
                    $participant->unit_kerja = $unitKerja;

                    if ($kabKota !== '') {
                        $participant->kabupaten_kota = mb_substr($kabKota, 0, 255);
                    }

                    if ($phone !== '') {
                        $participant->phone_number = mb_substr($phone, 0, 24);
                    }

                    // Kata sandi hanya dibuat untuk peserta baru; impor ulang tidak boleh
                    // mereset kata sandi yang sudah diubah peserta.
                    if ($isNew) {
                        $participant->password = $nip;
                    }

                    $participant->is_active = true;

                    if ($participant->trashed()) {
                        $participant->deleted_at = null;
                    }

                    $participant->save();

                    $isNew ? $created++ : $updated++;
                }
            });
        } catch (Throwable $e) {
            fclose($handle);
            report($e);

            return back()->with('error', 'Impor peserta gagal dan dibatalkan seluruhnya. Periksa format berkas CSV lalu coba lagi.');
        }

        fclose($handle);

        AuditLogger::log('participant_imported', null, [
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);

        $summary = "Berhasil mengimpor {$created} peserta baru dan memperbarui {$updated} data peserta.";
        if ($skipped > 0) {
            $summary .= " {$skipped} baris dilewati karena data tidak lengkap atau melebihi batas.";
        }

        return redirect()->route('admin.peserta.index')->with('success', $summary);
    }

    /**
     * Unduh template CSV untuk impor data peserta.
     */
    public function downloadTemplateCsv(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-impor-peserta.csv"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');

            // Tulis BOM UTF-8 agar karakter Indonesia dan aksen terbaca mulus di Microsoft Excel
            fwrite($handle, "\xEF\xBB\xBF");

            // Header kolom CSV
            fputcsv($handle, ['nip', 'name', 'unit_kerja', 'kabupaten_kota', 'phone_number']);

            // Baris contoh data nyata (panduan pengisian)
            fputcsv($handle, ['198507152010011003', 'Drs. H. Ahmad Sudrajat, M.Pd.', 'SMK Negeri 1 Majalengka', 'Kabupaten Majalengka', '081234567890']);
            fputcsv($handle, ['199203102019032014', 'Siti Nurhaliza, S.Kom.', 'Dinas Pendidikan Jawa Barat', 'Kabupaten Majalengka', '085712345678']);
            fputcsv($handle, ['3210015504820005', 'Budi Santoso (Wali Murid)', 'Komite Sekolah SMKN 1 Majalengka', 'Kabupaten Cirebon', '082198765432']);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Petakan kolom CSV berdasarkan judul, dengan fallback urutan standar.
     *
     * @return array<string, int>
     */
    private function mapCsvColumns(?array $header): array
    {
        $fallback = [
            'nip' => 0,
            'name' => 1,
            'unit_kerja' => 2,
            'kabupaten_kota' => 3,
            'phone_number' => 4,
        ];

        if (! $header) {
            return $fallback;
        }

        $normalized = array_map(function ($value) {
            $value = preg_replace('/^\xEF\xBB\xBF/', '', (string) $value);

            return trim((string) preg_replace('/[^a-z0-9]+/', ' ', strtolower((string) $value)));
        }, $header);

        $aliases = [
            'nip' => ['nip', 'nomor induk pegawai', 'no induk pegawai', 'no induk'],
            'name' => ['nama', 'nama lengkap', 'name'],
            'unit_kerja' => ['unit kerja', 'unit kerja instansi', 'unit', 'instansi'],
            'kabupaten_kota' => ['kabupaten kota', 'kabupaten', 'kota', 'kabupaten kota domisili'],
            'phone_number' => ['telepon', 'no telepon', 'nomor telepon', 'phone', 'telepon seluler', 'hp'],
        ];

        $mapped = [];
        foreach ($aliases as $field => $candidates) {
            foreach ($normalized as $index => $candidate) {
                if (in_array($candidate, $candidates, true)) {
                    $mapped[$field] = $index;
                    break;
                }
            }
        }

        return $mapped + $fallback;
    }
}
