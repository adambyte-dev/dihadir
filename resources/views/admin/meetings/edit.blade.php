<x-layouts.admin title="Ubah Pertemuan" heading="Ubah Pertemuan">
    <div class="w-full">
        <x-page-header
            :title="'Ubah: ' . $meeting->title"
            description="Perbarui rincian jadwal, lokasi, atau status publikasi formulir absensi."
            eyebrow="Manajemen Kegiatan"
            :back-route="route('admin.pertemuan.show', $meeting)"
            back-label="Rincian Pertemuan"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.pertemuan.update', $meeting) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                <x-form-section
                    title="Informasi Pokok Kegiatan"
                    description="Identitas kegiatan yang tampil pada formulir absensi peserta."
                    :step="1"
                >
                    <div class="space-y-4">
                        <x-input
                            label="Nama Kegiatan / Pertemuan"
                            name="title"
                            id="title"
                            type="text"
                            value="{{ old('title', $meeting->title) }}"
                            :required="true"
                            :error="$errors->first('title')"
                        />

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Penyelenggara"
                                name="organizer"
                                id="organizer"
                                type="text"
                                value="{{ old('organizer', $meeting->organizer) }}"
                                :required="true"
                                :error="$errors->first('organizer')"
                            />

                            <x-input
                                label="Lokasi / Ruangan"
                                name="location"
                                id="location"
                                type="text"
                                value="{{ old('location', $meeting->location) }}"
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
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >{{ old('description', $meeting->description) }}</textarea>
                            @error('description')
                                <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </x-form-section>

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
                            value="{{ old('meeting_date', $meeting->meeting_date->format('Y-m-d')) }}"
                            :required="true"
                            :error="$errors->first('meeting_date')"
                        />

                        <x-input
                            label="Waktu Mulai"
                            name="start_time"
                            id="start_time"
                            type="time"
                            value="{{ old('start_time', substr($meeting->start_time, 0, 5)) }}"
                            :required="true"
                            :error="$errors->first('start_time')"
                        />

                        <x-input
                            label="Waktu Selesai"
                            name="end_time"
                            id="end_time"
                            type="time"
                            value="{{ old('end_time', $meeting->end_time ? substr($meeting->end_time, 0, 5) : '') }}"
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
                                value="{{ old('max_participants', $meeting->max_participants) }}"
                                placeholder="Kosongkan jika tidak dibatasi"
                                :error="$errors->first('max_participants')"
                                helper="Kosongkan bila kuota tidak dibatasi."
                            />
                        </div>
                    </div>
                </x-form-section>

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
                                :value="old('status', $meeting->status)"
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
                                :value="old('form_status', $meeting->form_status)"
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
                                @checked(old('allow_anonymous', $meeting->allow_anonymous))
                                class="mt-0.5 h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                            >
                            <span class="min-w-0">
                                <span class="block text-[13px] font-semibold text-slate-800">Mode Absensi Terbuka</span>
                                <span class="mt-0.5 block text-[11.5px] leading-relaxed text-slate-500">
                                    Peserta dapat langsung mengisi formulir melalui tautan/QR tanpa login NIP.
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
                            @php $assignedAdminIds = old('admin_ids', $meeting->admins->pluck('id')->toArray()); @endphp
                            <div class="grid max-h-44 grid-cols-1 gap-1 overflow-y-auto rounded-lg border border-slate-200 p-2 sm:grid-cols-2">
                                @foreach ($admins as $admin)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-slate-50">
                                        <input
                                            type="checkbox"
                                            name="admin_ids[]"
                                            value="{{ $admin->id }}"
                                            @checked(in_array($admin->id, $assignedAdminIds))
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500"
                                        >
                                        <span class="truncate text-xs font-medium text-slate-700">{{ $admin->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-form-section>

                <div class="sticky bottom-0 -mx-1 flex items-center justify-end gap-2.5 rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 shadow-sm backdrop-blur-sm">
                    <x-button :href="route('admin.pertemuan.show', $meeting)" variant="outline">Batal</x-button>
                    <x-button type="submit" variant="primary">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Perbarui Pertemuan</span>
                    </x-button>
                </div>
                </div>

                {{-- Panel pendamping --}}
                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Ringkasan Pertemuan</h3>
                            <dl class="mt-3.5 space-y-2.5 text-[11.5px]">
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-2.5">
                                    <dt class="text-slate-500">Dibuat</dt>
                                    <dd class="text-right font-semibold text-slate-800">{{ $meeting->created_at?->translatedFormat('d M Y') ?? '-' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-2.5">
                                    <dt class="text-slate-500">Terakhir diubah</dt>
                                    <dd class="text-right font-semibold text-slate-800">{{ $meeting->updated_at?->diffForHumans() ?? '-' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-3 border-b border-slate-100 pb-2.5">
                                    <dt class="text-slate-500">Status agenda</dt>
                                    <dd class="text-right"><x-badge :variant="$meeting->status_badge_variant" size="sm">{{ $meeting->status_label }}</x-badge></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-slate-500">Status formulir</dt>
                                    <dd class="text-right"><x-badge :variant="$meeting->form_status_badge_variant" size="sm">{{ $meeting->form_status_label }}</x-badge></dd>
                                </div>
                            </dl>
                        </div>
                    </section>

                    <section class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm">
                        <h3 class="text-[13px] font-bold text-slate-900">Tautan Publik</h3>
                        <p class="mt-1 text-[11px] leading-relaxed text-slate-500">Tautan ini tetap sama meski rincian pertemuan diubah.</p>
                        <div class="mt-3 break-all rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 font-mono text-[10.5px] text-slate-700">
                            {{ $meeting->public_url }}
                        </div>
                        <a
                            href="{{ $meeting->public_url }}"
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-flex min-h-[44px] w-full items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white text-[11.5px] font-semibold text-slate-700 transition-colors hover:bg-slate-50"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            Buka Formulir Publik
                        </a>
                    </section>

                    <section class="rounded-xl border border-amber-200/80 bg-amber-50/70 p-4">
                        <div class="flex gap-2.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                            </svg>
                            <p class="text-[11.5px] leading-relaxed text-amber-900">
                                Mengubah rincian tidak membatalkan kehadiran yang sudah masuk. Kehadiran tetap terikat pada pertemuan ini.
                            </p>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
