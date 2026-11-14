<x-layouts.admin :title="'Form Builder: ' . $meeting->title" heading="Form Builder Presensi">
    <div class="space-y-6" x-data="{ mobileTab: 'preview' }">
        {{-- Breadcrumb & Header Navigasi --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <a
                    href="{{ route('admin.pertemuan.show', $meeting) }}"
                    class="mb-2 inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 transition-colors hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Rincian Pertemuan</span>
                </a>

                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                        {{ $meeting->title }}
                    </h2>
                    <x-badge :variant="$meeting->form_status_badge_variant" dot>
                        {{ $meeting->form_status_label }}
                    </x-badge>
                </div>
                <p class="mt-1 text-xs text-slate-500">
                    Susun, uji, dan kelola seluruh pertanyaan formulir presensi untuk kegiatan pertemuan ini.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <a
                    href="{{ route('absensi.form', $meeting->public_token) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                >
                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <span>Buka Pratinjau Publik</span>
                </a>

                <form method="POST" action="{{ route('admin.pertemuan.toggle-form', $meeting) }}">
                    @csrf
                    @if ($meeting->form_status === 'published')
                        <button
                            type="submit"
                            class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl border border-rose-200 bg-rose-50/50 px-4 text-xs font-semibold text-rose-700 transition-colors hover:bg-rose-100 hover:text-rose-800 focus:outline-none focus:ring-2 focus:ring-rose-500/20"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <span>Tutup Formulir</span>
                        </button>
                    @else
                        <button
                            type="submit"
                            class="inline-flex min-h-[44px] items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-semibold text-white shadow-2xs transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Terbitkan Formulir</span>
                        </button>
                    @endif
                </form>
            </div>
        </div>

        {{-- Tab Navigasi Mobile (Beralih Cepat antara Formulir & Pengaturan Kolom di HP) --}}
        <div class="flex rounded-xl border border-slate-200 bg-white p-1 shadow-2xs lg:hidden">
            <button
                type="button"
                @click="mobileTab = 'preview'"
                :class="mobileTab === 'preview' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                class="flex-1 rounded-lg py-2.5 text-center text-xs font-bold transition"
            >
                Pratinjau Pertanyaan ({{ $meeting->formFields->count() }})
            </button>
            <button
                type="button"
                @click="mobileTab = 'tools'"
                :class="mobileTab === 'tools' ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                class="flex-1 rounded-lg py-2.5 text-center text-xs font-bold transition"
            >
                + Tambah &amp; Template
            </button>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Kolom Kiri: Pratinjau Formulir Presensi --}}
            <div
                class="space-y-5 lg:col-span-7"
                :class="mobileTab === 'preview' ? 'block' : 'hidden lg:block'"
            >
                {{-- Header Identitas Pertemuan --}}
                <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs">
                    <div class="flex items-start gap-4 pb-4 border-b border-slate-100">
                        <img
                            src="{{ asset('images/logo-smkn1majalengka.webp') }}"
                            alt="Lambang SMKN 1 Majalengka"
                            class="h-14 w-auto shrink-0 object-contain drop-shadow-2xs"
                        >
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                Formulir Kehadiran Pertemuan
                            </p>
                            <h3 class="mt-0.5 text-lg font-bold text-slate-900 leading-snug">
                                {{ $meeting->title }}
                            </h3>
                            <p class="mt-1 text-xs text-slate-600 leading-relaxed whitespace-pre-line">
                                {{ $meeting->description ?: 'Isi daftar presensi berikut secara lengkap sebagai bukti kehadiran resmi dalam kegiatan pertemuan.' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3.5 flex flex-wrap items-center gap-y-2 gap-x-4 text-xs text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span><strong class="font-semibold text-slate-700">Penyelenggara:</strong> {{ $meeting->organizer }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span><strong class="font-semibold text-slate-700">Lokasi:</strong> {{ $meeting->location }}</span>
                        </div>
                        <span class="inline-flex items-center text-rose-600 font-medium text-[11px]">
                            * Menandakan pertanyaan wajib diisi
                        </span>
                    </div>
                </div>

                {{-- Daftar Pertanyaan Formulir --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between px-1">
                        <div>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                Daftar Pertanyaan Formulir ({{ $meeting->formFields->count() }})
                            </h4>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Seluruh pertanyaan dinamis yang akan dijawab oleh peserta saat presensi.
                            </p>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">
                            Urutan tampilan
                        </span>
                    </div>

                    @forelse ($meeting->formFields as $field)
                        <div
                            class="relative rounded-2xl border {{ $field->is_active ? 'border-slate-200/90 bg-white' : 'border-slate-200/60 bg-slate-50/70 opacity-60' }} p-4 shadow-2xs transition-all hover:border-slate-300"
                            x-data="{ editing: false }"
                        >
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="flex h-5 w-5 items-center justify-center rounded-md bg-slate-100 text-[11px] font-bold text-slate-700">
                                            {{ $loop->iteration }}
                                        </span>
                                        <h5 class="text-sm font-bold text-slate-900">
                                            {{ $field->label }}
                                            @if ($field->is_required)
                                                <span class="text-rose-500 font-bold" title="Wajib Diisi">*</span>
                                            @endif
                                        </h5>
                                        <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                            {{ $field->type_label }}
                                        </span>
                                        @if (! $field->is_active)
                                            <span class="rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 text-[10px] font-semibold text-amber-700">
                                                Nonaktif (Disembunyikan)
                                            </span>
                                        @endif
                                    </div>

                                    @if ($field->description)
                                        <p class="mt-1.5 text-xs text-slate-500 leading-relaxed">{{ $field->description }}</p>
                                    @endif

                                    {{-- Pratinjau Komponen Input Nyata --}}
                                    <div class="mt-3 pointer-events-none opacity-85">
                                        @if (in_array($field->type, ['text', 'number', 'email', 'phone']))
                                            <div class="h-9 w-full max-w-sm rounded-xl border border-slate-200 bg-slate-50/70 px-3 text-xs text-slate-400 flex items-center">
                                                {{ $field->placeholder ?: ($field->type === 'email' ? 'alamat@email.com' : ($field->type === 'phone' ? '08xxxxxxxxxx' : 'Jawaban teks singkat...')) }}
                                            </div>
                                        @elseif ($field->type === 'textarea')
                                            <div class="h-16 w-full rounded-xl border border-slate-200 bg-slate-50/70 p-3 text-xs text-slate-400">
                                                {{ $field->placeholder ?: 'Tuliskan catatan atau keterangan lengkap di sini...' }}
                                            </div>
                                        @elseif ($field->type === 'file')
                                            <div class="flex items-center gap-2 rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-3 text-xs text-slate-600">
                                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                </svg>
                                                <span>Pilih berkas untuk diunggah (Gambar / PDF)</span>
                                            </div>
                                        @elseif ($field->type === 'signature')
                                            <div class="flex items-center gap-2 rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-3 text-xs text-slate-600">
                                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                                <span>Kanvas Tanda Tangan Digital (Touchscreen / Mouse)</span>
                                            </div>
                                        @elseif ($field->type === 'date')
                                            <div class="h-9 w-44 rounded-xl border border-slate-200 bg-slate-50/70 px-3 text-xs text-slate-400 flex items-center">
                                                TTTT-BB-HH (Kalender)
                                            </div>
                                        @elseif ($field->type === 'time')
                                            <div class="h-9 w-32 rounded-xl border border-slate-200 bg-slate-50/70 px-3 text-xs text-slate-400 flex items-center">
                                                --:-- WIB
                                            </div>
                                        @elseif ($field->type === 'scale')
                                            <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                                                @foreach ($field->options ?? ['1', '2', '3', '4', '5'] as $sOpt)
                                                    <span class="rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-slate-700 font-semibold">{{ $sOpt }}</span>
                                                @endforeach
                                            </div>
                                        @elseif (in_array($field->type, ['select', 'radio', 'checkbox']))
                                            <div class="space-y-1.5 text-xs text-slate-600">
                                                @foreach ($field->options ?? [] as $opt)
                                                    <div class="flex items-center gap-2">
                                                        <span class="h-3.5 w-3.5 rounded-full border border-slate-300 bg-white"></span>
                                                        <span>{{ $opt }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Aksi Field: Naik, Turun, Toggle Aktif, Edit, Hapus --}}
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <form method="POST" action="{{ route('admin.pertemuan.builder.reorder', [$meeting, $field, 'up']) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-2xs hover:bg-slate-50 hover:text-slate-800 disabled:opacity-40 disabled:pointer-events-none"
                                            title="Geser ke Atas"
                                            {{ $loop->first ? 'disabled' : '' }}
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.pertemuan.builder.reorder', [$meeting, $field, 'down']) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 shadow-2xs hover:bg-slate-50 hover:text-slate-800 disabled:opacity-40 disabled:pointer-events-none"
                                            title="Geser ke Bawah"
                                            {{ $loop->last ? 'disabled' : '' }}
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </form>

                                    {{-- Toggle Aktif / Nonaktif --}}
                                    <form method="POST" action="{{ route('admin.pertemuan.builder.toggle', [$meeting, $field]) }}" class="inline">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border {{ $field->is_active ? 'border-slate-200 bg-white text-slate-600 hover:text-amber-600' : 'border-amber-300 bg-amber-50 text-amber-700' }} shadow-2xs"
                                            title="{{ $field->is_active ? 'Sembunyikan dari formulir (Nonaktifkan)' : 'Tampilkan di formulir (Aktifkan)' }}"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                @if ($field->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>
                                                @endif
                                            </svg>
                                        </button>
                                    </form>

                                    <button
                                        type="button"
                                        @click="editing = !editing"
                                        class="inline-flex h-8 items-center rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:text-blue-600"
                                    >
                                        Edit
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.pertemuan.builder.destroy', [$meeting, $field]) }}"
                                        class="inline"
                                        data-confirm="Hapus pertanyaan '{{ $field->label }}' dari formulir? Pertanyaan yang belum memiliki jawaban akan dihapus secara permanen."
                                        data-confirm-title="Hapus Pertanyaan"
                                        data-confirm-variant="danger"
                                        data-confirm-btn="Ya, Hapus"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 bg-white text-rose-600 shadow-2xs hover:bg-rose-50 hover:border-rose-200"
                                            title="Hapus Pertanyaan"
                                        >
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Inline Edit Drawer/Form --}}
                            <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-slate-200">
                                <form method="POST" action="{{ route('admin.pertemuan.builder.update', [$meeting, $field]) }}" class="space-y-3.5">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Pertanyaan</label>
                                            <input type="text" name="label" value="{{ $field->label }}" required class="w-full h-9 rounded-xl border border-slate-300 px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Input</label>
                                            <x-custom-select
                                                name="type"
                                                :value="$field->type"
                                                size="sm"
                                                :options="[
                                                    'text' => 'Teks Singkat (Short Text)',
                                                    'textarea' => 'Paragraf / Catatan (Long Text)',
                                                    'number' => 'Angka / Nominal (Number)',
                                                    'email' => 'Alamat Email',
                                                    'phone' => 'Nomor WhatsApp / Telepon',
                                                    'select' => 'Dropdown Pilihan (Select)',
                                                    'radio' => 'Pilihan Tunggal (Radio)',
                                                    'checkbox' => 'Kotak Centang (Bisa Banyak)',
                                                    'file' => 'Unggah Foto / Dokumen',
                                                    'signature' => 'Tanda Tangan Digital (Signature Canvas)',
                                                    'date' => 'Pilihan Tanggal (Date)',
                                                    'time' => 'Pilihan Waktu / Jam (Time)',
                                                    'scale' => 'Skala Linier (Rating / Kepuasan)',
                                                ]"
                                            />
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Petunjuk (Opsional)</label>
                                        <input type="text" name="description" value="{{ $field->description }}" class="w-full h-9 rounded-xl border border-slate-300 px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                                    </div>

                                    @if (in_array($field->type, ['select', 'radio', 'checkbox', 'scale']))
                                        <div>
                                            <label class="block text-xs font-semibold text-slate-700 mb-1">Opsi Pilihan (Pisahkan 1 baris per opsi)</label>
                                            <textarea name="options_raw" rows="3" class="w-full rounded-xl border border-slate-300 p-2.5 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500">{{ implode("\n", $field->options ?? []) }}</textarea>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between pt-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="is_required" value="1" {{ $field->is_required ? 'checked' : '' }} class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                            <span class="text-xs font-semibold text-slate-700">Wajib Diisi</span>
                                        </label>

                                        <div class="flex gap-2">
                                            <button type="button" @click="editing = false" class="min-h-[38px] px-3.5 text-xs font-semibold rounded-xl border border-slate-300 bg-white text-slate-600 hover:bg-slate-50">Batal</button>
                                            <x-button type="submit" variant="primary" size="sm" class="min-h-[38px]">Simpan Perubahan</x-button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-slate-500 shadow-2xs">
                            <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="font-bold text-slate-800 text-sm">Belum Ada Pertanyaan di Formulir Ini</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Tambahkan pertanyaan baru melalui panel di sebelah kanan, atau pilih paket template siap pakai untuk memulai.
                            </p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Kanan: Panel Tambah Pertanyaan & Pustaka Template --}}
            <div
                class="space-y-5 lg:col-span-5"
                :class="mobileTab === 'tools' ? 'block' : 'hidden lg:block'"
            >
                {{-- Form Tambah Pertanyaan Baru --}}
                <div class="space-y-5">
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs" x-data="{ selectedType: 'text' }">
                        <div class="flex items-center gap-2.5 pb-3.5 border-b border-slate-100">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-blue-600 text-white shadow-2xs">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Tambah Pertanyaan Baru</h3>
                                <p class="text-[11px] text-slate-500">Tambahkan kolom pertanyaan ke formulir pertemuan ini.</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin.pertemuan.builder.store', $meeting) }}" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Label Pertanyaan <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <input
                                    type="text"
                                    name="label"
                                    required
                                    placeholder="Contoh: Nama Anak / Nomor WhatsApp / Masukan"
                                    class="w-full h-10 rounded-xl border border-slate-300 px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                >
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Tipe Input Pertanyaan <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <x-custom-select
                                    name="type"
                                    id="builder_new_type"
                                    :value="'text'"
                                    :required="true"
                                    @change="selectedType = $event.detail"
                                    :options="[
                                        'text' => 'Teks Singkat (Short Text)',
                                        'textarea' => 'Paragraf / Catatan Panjang (Long Text)',
                                        'number' => 'Angka / Nilai Nominal (Number)',
                                        'email' => 'Alamat Email Resmi',
                                        'phone' => 'Nomor WhatsApp / Kontak Telepon',
                                        'select' => 'Dropdown Menu (Pilih Satu)',
                                        'radio' => 'Pilihan Tunggal (Radio Button)',
                                        'checkbox' => 'Kotak Centang (Bisa Pilih Banyak)',
                                        'file' => 'Unggah Berkas / Dokumen / Foto',
                                        'signature' => 'Tanda Tangan Digital',
                                        'date' => 'Pilihan Tanggal (Date)',
                                        'time' => 'Pilihan Waktu / Jam (Time)',
                                        'scale' => 'Skala Kepuasan / Penilaian (1-5)',
                                    ]"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Petunjuk Pengisian (Opsional)
                                </label>
                                <input
                                    type="text"
                                    name="description"
                                    placeholder="Contoh: Tuliskan nama lengkap sesuai dokumen resmi"
                                    class="w-full h-10 rounded-xl border border-slate-300 px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                >
                            </div>

                            <div x-show="['select', 'radio', 'checkbox', 'scale'].includes(selectedType)" x-cloak>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Daftar Opsi Pilihan (Tulis 1 baris per opsi) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <textarea
                                    name="options_raw"
                                    rows="4"
                                    placeholder="Opsi Pilihan 1&#10;Opsi Pilihan 2&#10;Opsi Pilihan 3"
                                    class="w-full rounded-xl border border-slate-300 p-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                ></textarea>
                                <p class="mt-1 text-[11px] text-slate-500" x-show="selectedType === 'scale'">
                                    Untuk Skala Linier, biarkan kosong untuk memakai opsi bawaan (1 s/d 5) atau tuliskan label tingkatan sendiri.
                                </p>
                            </div>

                            <div class="flex items-center justify-between rounded-xl border border-slate-200/80 bg-slate-50/70 p-3">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input
                                        type="checkbox"
                                        name="is_required"
                                        value="1"
                                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <span class="text-xs font-semibold text-slate-800">Pertanyaan Wajib Diisi</span>
                                </label>
                                <span class="text-[11px] text-slate-400">Validasi server</span>
                            </div>

                            <button
                                type="submit"
                                class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-xs font-semibold text-white shadow-2xs transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Simpan ke Formulir</span>
                            </button>
                        </form>
                    </div>

                    {{-- Template Formulir Cepat SMKN 1 Majalengka --}}
                    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-2xs">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-slate-100 text-slate-700">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Paket Template Siap Pakai</h4>
                                <p class="text-[11px] text-slate-500">Terapkan pertanyaan terpadu sesuai jenis kegiatan.</p>
                            </div>
                        </div>

                        <div class="mt-3.5 space-y-3">
                            @foreach ($templates as $tKey => $tmpl)
                                <div class="rounded-xl border border-slate-200/90 bg-slate-50/50 p-3.5 transition-colors hover:border-slate-300">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0 flex-1">
                                            <h5 class="text-xs font-bold text-slate-900 leading-snug">{{ $tmpl['title'] }}</h5>
                                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ $tmpl['description'] }}</p>
                                            <span class="inline-block mt-2 rounded-md bg-white border border-slate-200 px-2 py-0.5 text-[10px] font-semibold text-slate-600">
                                                {{ count($tmpl['fields']) }} kolom pertanyaan
                                            </span>
                                        </div>
                                        <form
                                            method="POST"
                                            action="{{ route('admin.pertemuan.builder.template', $meeting) }}"
                                            data-confirm="Terapkan paket pertanyaan '{{ $tmpl['title'] }}' ke formulir pertemuan ini? Pertanyaan baru dari template akan ditambahkan ke formulir."
                                            data-confirm-title="Terapkan Template"
                                            data-confirm-variant="primary"
                                            data-confirm-btn="Terapkan Sekarang"
                                        >
                                            @csrf
                                            <input type="hidden" name="template_key" value="{{ $tKey }}">
                                            <button
                                                type="submit"
                                                class="inline-flex min-h-[36px] shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-blue-600"
                                            >
                                                Terapkan
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>