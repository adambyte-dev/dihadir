<x-layouts.admin title="Tambah Pertemuan Baru" heading="Tambah Pertemuan">
    <div class="w-full">
        <x-page-header
            title="Tambah Pertemuan Baru"
            description="Lengkapi rincian kegiatan untuk menyiapkan formulir presensi digital dan tautan publik."
            eyebrow="Manajemen Kegiatan"
            :back-route="route('admin.pertemuan.index')"
            back-label="Daftar Pertemuan"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.pertemuan.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                {{-- 1. Informasi pokok --}}
                <x-form-section
                    title="Informasi Pokok Kegiatan"
                    description="Identitas kegiatan yang akan tampil pada formulir absensi peserta."
                    :step="1"
                >
                    <div class="space-y-4">
                        <x-input
                            label="Nama Kegiatan / Pertemuan"
                            name="title"
                            id="title"
                            type="text"
                            value="{{ old('title') }}"
                            placeholder="Contoh: Rapat Pleno Komite & Orang Tua Siswa Kelas X"
                            :required="true"
                            :error="$errors->first('title')"
                            autofocus
                        />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Penyelenggara"
                                name="organizer"
                                id="organizer"
                                type="text"
                                value="{{ old('organizer', 'SMKN 1 Majalengka') }}"
                                placeholder="SMKN 1 Majalengka"
                                :required="true"
                                :error="$errors->first('organizer')"
                            />

                            <x-input
                                label="Lokasi / Ruangan"
                                name="location"
                                id="location"
                                type="text"
                                value="{{ old('location') }}"
                                placeholder="Contoh: Aula Utama / Lab Komputer 1"
                                :required="true"
                                :error="$errors->first('location')"
                            />
                        </div>

                        <div>
                            <label for="description" class="mb-1.5 block text-[13px] font-semibold text-slate-700">
                                Deskripsi / Catatan Agenda
                                <span class="ml-1 text-[11px] font-normal text-slate-500">Opsional</span>
                            </label>
                            <textarea
                                id="description"
                                name="description"
                                rows="3"
                                placeholder="Jelaskan agenda pembahasan kegiatan…"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-form-section>

                {{-- 2. Waktu & kuota --}}
                <x-form-section
                    title="Waktu & Kuota Pertemuan"
                    description="Jadwal pelaksanaan serta batas jumlah peserta bila diperlukan."
                    :step="2"
                >
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <x-input
                            label="Tanggal Kegiatan"
                            name="meeting_date"
                            id="meeting_date"
                            type="date"
                            value="{{ old('meeting_date', date('Y-m-d')) }}"
                            :required="true"
                            :error="$errors->first('meeting_date')"
                        />

                        <x-input
                            label="Waktu Mulai"
                            name="start_time"
                            id="start_time"
                            type="time"
                            value="{{ old('start_time', '08:00') }}"
                            :required="true"
                            :error="$errors->first('start_time')"
                        />

                        <x-input
                            label="Waktu Selesai"
                            name="end_time"
                            id="end_time"
                            type="time"
                            value="{{ old('end_time', '12:00') }}"
                            :error="$errors->first('end_time')"
                            helper="Boleh dikosongkan jika fleksibel"
                        />
                    </div>

                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <div class="sm:max-w-xs">
                            <x-input
                                label="Batas Maksimum Peserta"
                                name="max_participants"
                                id="max_participants"
                                type="number"
                                min="1"
                                value="{{ old('max_participants') }}"
                                placeholder="Kosongkan jika tidak dibatasi"
                                :error="$errors->first('max_participants')"
                                helper="Kosongkan bila kuota tidak dibatasi."
                            />
                        </div>
                    </div>
                </x-form-section>

                {{-- 3. Status & akses --}}
                <x-form-section
                    title="Status & Hak Akses"
                    description="Tentukan publikasi formulir dan cara peserta mengakses absensi."
                    :step="3"
                >
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <x-custom-select
                                label="Status Agenda Kegiatan"
                                name="status"
                                :value="old('status', 'scheduled')"
                                :required="true"
                                :error="$errors->first('status')"
                                :options="[
                                    'scheduled' => 'Terjadwal',
                                    'ongoing' => 'Sedang Berlangsung',
                                    'draft' => 'Draf',
                                    'completed' => 'Selesai',
                                    'archived' => 'Diarsipkan',
                                ]"
                            />
                        </div>

                        <div>
                            <x-custom-select
                                label="Status Publikasi Formulir"
                                name="form_status"
                                :value="old('form_status', 'draft')"
                                :required="true"
                                :error="$errors->first('form_status')"
                                :options="[
                                    'draft' => 'Draf: tautan publik belum menerima absensi',
                                    'published' => 'Terbit: menerima pengisian kehadiran',
                                    'closed' => 'Ditutup: menolak absensi baru',
                                ]"
                            />
                        </div>
                    </div>

                    <div class="mt-4 border-t border-slate-100 pt-4">
                        <label class="flex cursor-pointer select-none items-start gap-3 rounded-lg border border-slate-200 bg-slate-50/60 p-3.5 transition-colors hover:border-slate-300 hover:bg-slate-50">
                            <input
                                type="checkbox"
                                name="allow_anonymous"
                                value="1"
                                @checked(old('allow_anonymous'))
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            >
                            <span class="min-w-0">
                                <span class="block text-[13px] font-semibold text-slate-800">Mode Absensi Terbuka</span>
                                <span class="mt-0.5 block text-[11.5px] leading-relaxed text-slate-500">
                                    Peserta dapat langsung mengisi formulir melalui tautan/QR tanpa login NIP. Cocok untuk pertemuan orang tua bersifat umum.
                                </span>
                            </span>
                        </label>
                    </div>

                    @if (auth()->user()->isSuperAdmin() && $admins->isNotEmpty())
                        <div class="mt-4 border-t border-slate-100 pt-4">
                            <p class="mb-2 text-[13px] font-semibold text-slate-700">
                                Penugasan Admin Pengelola
                                <span class="ml-1 text-[11px] font-normal text-slate-500">Opsional</span>
                            </p>
                            <div class="grid max-h-44 grid-cols-1 gap-1 overflow-y-auto rounded-lg border border-slate-200 p-2 sm:grid-cols-2">
                                @foreach ($admins as $admin)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-slate-50">
                                        <input
                                            type="checkbox"
                                            name="admin_ids[]"
                                            value="{{ $admin->id }}"
                                            @checked(in_array($admin->id, old('admin_ids', [])))
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                        >
                                        <span class="truncate text-xs font-medium text-slate-700">{{ $admin->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-form-section>

                {{-- Aksi --}}
                <div class="sticky bottom-0 -mx-1 flex items-center justify-end gap-2.5 rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 shadow-sm backdrop-blur-sm">
                    <x-button :href="route('admin.pertemuan.index')" variant="outline">Batal</x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Pertemuan</span>
                    </x-button>
                </div>
                </div>

                {{-- Panel pendamping --}}
                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Panduan Pengisian</h3>
                            <p class="mt-0.5 text-[11px] text-slate-500">Empat hal yang perlu dipastikan sebelum menyimpan.</p>

                            <ul class="mt-4 space-y-3">
                                @foreach ([
                                    ['Nama kegiatan jelas', 'Tulis nama yang dikenali peserta, misalnya "Rapat Pleno Komite Kelas X".'],
                                    ['Jadwal akurat', 'Tanggal dan jam memakai zona Asia/Jakarta (WIB) dan menjadi acuan masa berlaku formulir.'],
                                    ['Status formulir tepat', 'Pilih Terbit hanya ketika formulir benar-benar siap menerima kehadiran.'],
                                    ['Lokasi spesifik', 'Sebutkan ruangan agar peserta tidak salah tempat.'],
                                ] as $tip)
                                    <li class="flex gap-2.5">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-bold leading-snug text-slate-800">{{ $tip[0] }}</p>
                                            <p class="mt-0.5 text-[11px] leading-relaxed text-slate-500">{{ $tip[1] }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm">
                        <h3 class="text-[13px] font-bold text-slate-900">Setelah Disimpan</h3>
                        <ol class="mt-3 space-y-2.5">
                            @foreach ([
                                'Sistem membuat tautan publik dan QR Code unik.',
                                'Bagikan tautan/QR ke peserta pertemuan.',
                                'Kehadiran masuk otomatis ke Rekap Kehadiran.',
                                'Unduh berita acara PDF ber-KOP sekolah.',
                            ] as $index => $step)
                                <li class="flex gap-2.5">
                                    <span class="flex h-5 w-5 shrink-0 items-center justify-center rounded bg-slate-900 text-[10px] font-bold text-white">{{ $index + 1 }}</span>
                                    <p class="text-[11.5px] leading-relaxed text-slate-600">{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </section>

                    <section class="rounded-xl border border-blue-200/80 bg-blue-50/70 p-4">
                        <div class="flex gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-[11.5px] leading-relaxed text-blue-900">
                                Formulir berstatus <span class="font-bold">Draf</span> tidak menerima absensi. Peserta yang membuka tautan akan melihat keterangan bahwa formulir belum dibuka.
                            </p>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
