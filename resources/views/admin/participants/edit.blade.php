<x-layouts.admin title="Ubah Data Peserta" heading="Ubah Peserta">
    <div class="w-full">
        <x-page-header
            :title="'Ubah: ' . $participant->name"
            description="Perbarui identitas, unit kerja, atau atur ulang kata sandi peserta."
            eyebrow="Manajemen Peserta"
            :back-route="route('admin.peserta.index')"
            back-label="Data Peserta"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.peserta.update', $participant) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                <x-form-section title="Identitas Peserta" description="Data ini menjadi acuan pencocokan kehadiran." :step="1">
                    <div class="space-y-4">
                        <x-input
                            label="NIP (Nomor Induk Pegawai)"
                            name="nip"
                            id="nip"
                            type="text"
                            value="{{ old('nip', $participant->nip) }}"
                            :required="true"
                            :error="$errors->first('nip')"
                            inputmode="numeric"
                        />

                        <x-input
                            label="Nama Lengkap"
                            name="name"
                            id="name"
                            type="text"
                            value="{{ old('name', $participant->name) }}"
                            :required="true"
                            :error="$errors->first('name')"
                        />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Unit Kerja / Instansi"
                                name="unit_kerja"
                                id="unit_kerja"
                                type="text"
                                value="{{ old('unit_kerja', $participant->unit_kerja) }}"
                                :required="true"
                                :error="$errors->first('unit_kerja')"
                            />

                            <x-input
                                label="Kabupaten / Kota"
                                name="kabupaten_kota"
                                id="kabupaten_kota"
                                type="text"
                                value="{{ old('kabupaten_kota', $participant->kabupaten_kota) }}"
                                :error="$errors->first('kabupaten_kota')"
                            />
                        </div>

                        <x-input
                            label="Nomor WhatsApp / HP"
                            name="phone_number"
                            id="phone_number"
                            type="text"
                            value="{{ old('phone_number', $participant->phone_number) }}"
                            :error="$errors->first('phone_number')"
                            inputmode="tel"
                        />
                    </div>
                </x-form-section>

                <x-form-section title="Akun & Akses" description="Kosongkan kata sandi bila tidak ingin mengubahnya." :step="2">
                    <div class="space-y-4">
                        <x-input
                            label="Kata Sandi Baru"
                            name="password"
                            id="password"
                            type="password"
                            placeholder="Biarkan kosong jika tidak diubah"
                            :error="$errors->first('password')"
                            helper="Minimal 6 karakter. Perubahan akan langsung berlaku pada login berikutnya."
                            autocomplete="new-password"
                        />

                        <label class="flex cursor-pointer select-none items-start gap-3 rounded-lg border border-slate-200 bg-slate-50/60 p-3.5 transition-colors hover:border-slate-300 hover:bg-slate-50">
                            {{-- Nilai 0 wajib dikirim agar menonaktifkan akun benar-benar tersimpan. --}}
                            <input type="hidden" name="is_active" value="0">
                            <input
                                type="checkbox"
                                name="is_active"
                                value="1"
                                @checked(old('is_active', $participant->is_active))
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            >
                            <span class="min-w-0">
                                <span class="block text-[13px] font-semibold text-slate-800">Akun Aktif</span>
                                <span class="mt-0.5 block text-[11.5px] leading-relaxed text-slate-500">
                                    Peserta dapat login dan mengisi formulir kehadiran.
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
                        <span>Simpan Perubahan</span>
                    </x-button>
                </div>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Ringkasan Peserta</h3>
                            <dl class="mt-3.5 space-y-2.5 text-[11.5px]">
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-2.5">
                                    <dt class="text-slate-500">Terdaftar</dt>
                                    <dd class="text-right font-semibold text-slate-800">{{ $participant->created_at?->translatedFormat('d M Y') ?? '-' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-2.5">
                                    <dt class="text-slate-500">Status akun</dt>
                                    <dd class="text-right">
                                        <x-badge :variant="$participant->is_active ? 'success' : 'default'" size="sm" dot>
                                            {{ $participant->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </x-badge>
                                    </dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-slate-500">Total kehadiran</dt>
                                    <dd class="tabular text-right font-semibold text-slate-800">{{ number_format($participant->attendances()->count()) }} pertemuan</dd>
                                </div>
                            </dl>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm">
                        <h3 class="text-[13px] font-bold text-slate-900">Perhatian</h3>
                        <ul class="mt-3 space-y-2.5">
                            @foreach ([
                                'Menonaktifkan akun tidak menghapus riwayat kehadiran.',
                                'Kata sandi hanya berubah bila kolomnya diisi.',
                                'NIP menjadi kunci pencocokan pada rekap dan laporan.',
                            ] as $note)
                                <li class="flex gap-2.5">
                                    <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 9v2m0 4h.01"/>
                                    </svg>
                                    <p class="text-[11.5px] leading-relaxed text-slate-600">{{ $note }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
