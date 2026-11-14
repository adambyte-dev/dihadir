<x-layouts.admin title="Tambah Peserta Baru" heading="Tambah Peserta">
    <div class="w-full">
        <x-page-header
            title="Tambah Peserta Baru"
            description="Daftarkan data orang tua, guru, atau peserta kegiatan agar dapat mengisi absensi."
            eyebrow="Manajemen Peserta"
            :back-route="route('admin.peserta.index')"
            back-label="Data Peserta"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.peserta.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                <x-form-section title="Identitas Peserta" description="Data ini menjadi acuan pencocokan kehadiran." :step="1">
                    <div class="space-y-4">
                        <x-input
                            label="NIP (Nomor Induk Pegawai)"
                            name="nip"
                            id="nip"
                            type="text"
                            value="{{ old('nip') }}"
                            placeholder="Contoh: 198205122010011005"
                            :required="true"
                            :error="$errors->first('nip')"
                            inputmode="numeric"
                            autofocus
                        />

                        <x-input
                            label="Nama Lengkap"
                            name="name"
                            id="name"
                            type="text"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Dra. Hj. Siti Nurjanah, M.Pd."
                            :required="true"
                            :error="$errors->first('name')"
                        />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Unit Kerja / Instansi"
                                name="unit_kerja"
                                id="unit_kerja"
                                type="text"
                                value="{{ old('unit_kerja', 'SMKN 1 Majalengka') }}"
                                placeholder="Contoh: SMKN 1 Majalengka"
                                :required="true"
                                :error="$errors->first('unit_kerja')"
                            />

                            <x-input
                                label="Kabupaten / Kota"
                                name="kabupaten_kota"
                                id="kabupaten_kota"
                                type="text"
                                value="{{ old('kabupaten_kota', 'Kab. Majalengka') }}"
                                placeholder="Contoh: Kab. Majalengka"
                                :error="$errors->first('kabupaten_kota')"
                            />
                        </div>

                        <x-input
                            label="Nomor WhatsApp / HP"
                            name="phone_number"
                            id="phone_number"
                            type="text"
                            value="{{ old('phone_number') }}"
                            placeholder="Contoh: 081234567890"
                            :error="$errors->first('phone_number')"
                            inputmode="tel"
                            helper="Opsional, digunakan panitia untuk konfirmasi kehadiran."
                        />
                    </div>
                </x-form-section>

                <x-form-section title="Akun & Akses" description="Peserta masuk ke formulir absensi memakai NIP dan kata sandi ini." :step="2">
                    <div class="space-y-4">
                        <x-input
                            label="Kata Sandi Awal"
                            name="password"
                            id="password"
                            type="password"
                            placeholder="Kosongkan untuk memakai NIP sebagai kata sandi"
                            :error="$errors->first('password')"
                            helper="Bila dikosongkan, kata sandi awal otomatis sama dengan NIP."
                            autocomplete="new-password"
                        />

                        <label class="flex cursor-pointer select-none items-start gap-3 rounded-lg border border-slate-200 bg-slate-50/60 p-3.5 transition-colors hover:border-slate-300 hover:bg-slate-50">
                            {{-- Nilai 0 wajib dikirim agar menonaktifkan akun benar-benar tersimpan. --}}
                            <input type="hidden" name="is_active" value="0">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                checked
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            >
                            <span class="min-w-0">
                                <span class="block text-[13px] font-semibold text-slate-800">Akun Aktif</span>
                                <span class="mt-0.5 block text-[11.5px] leading-relaxed text-slate-500">
                                    Peserta dapat login dan mengisi formulir kehadiran. Nonaktifkan bila peserta tidak lagi berhak.
                                </span>
                            </span>
                        </label>
                    </div>
                </x-form-section>

                <div class="sticky bottom-0 -mx-1 flex items-center justify-end gap-2.5 rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 shadow-sm backdrop-blur-sm">
                    <x-button :href="route('admin.peserta.index')" variant="outline">Batal</x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Simpan Peserta</span>
                    </x-button>
                </div>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Ketentuan Data Peserta</h3>
                            <ul class="mt-3.5 space-y-3">
                                @foreach ([
                                    ['NIP unik', 'Satu NIP hanya boleh terdaftar satu kali. Sistem menolak duplikasi.'],
                                    ['Nama sesuai identitas', 'Gunakan nama lengkap agar rekap dan laporan resmi konsisten.'],
                                    ['Kata sandi awal', 'Bila dikosongkan, kata sandi awal otomatis sama dengan NIP.'],
                                    ['Data pribadi', 'Nomor HP bersifat opsional dan hanya dipakai panitia untuk konfirmasi.'],
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

                    <section class="rounded-xl border border-blue-200/80 bg-blue-50/70 p-4">
                        <div class="flex gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-[11.5px] leading-relaxed text-blue-900">
                                Butuh menambahkan banyak peserta sekaligus? Gunakan <span class="font-bold">Impor Peserta</span> pada halaman Data Peserta.
                            </p>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
