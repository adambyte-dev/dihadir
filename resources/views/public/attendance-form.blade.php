<x-layouts.guest :title="'Presensi: ' . $meeting->title" max-width="max-w-3xl">
    <div class="mx-auto w-full max-w-2xl">

        {{-- ============ KEPALA HALAMAN ============ --}}
        <header class="mb-6 text-center">
            <div class="inline-flex items-center gap-2 rounded-md border border-blue-200 bg-blue-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-blue-700">
                <span class="relative flex h-1.5 w-1.5" aria-hidden="true">
                    <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                </span>
                Formulir Presensi Resmi
            </div>

            <h1 class="mt-2.5 text-[24px] font-extrabold leading-tight tracking-tight text-slate-900 sm:text-[28px]">
                {{ $meeting->title }}
            </h1>
            <p class="mx-auto mt-1.5 max-w-md text-[13px] leading-relaxed text-slate-500">
                {{ $meeting->description ?: 'Pencatatan kehadiran digital kegiatan resmi di lingkungan SMK Negeri 1 Majalengka.' }}
            </p>
        </header>

        {{-- ============ KARTU IDENTITAS PERTEMUAN ============ --}}
        <section class="relative mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="h-1 bg-blue-600" aria-hidden="true"></div>

            <div class="p-5 sm:p-6">
                <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.08em] text-slate-600 ring-1 ring-inset ring-slate-200">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        {{ $meeting->organizer }}
                    </span>
                    <span class="font-mono text-[10px] font-semibold uppercase tracking-wider text-slate-500">
                        Kode&nbsp;{{ substr($meeting->public_token, 0, 8) }}
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-3 border-t border-slate-100 pt-3 sm:grid-cols-2">
                    <div class="flex items-start gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Hari &amp; Tanggal</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-800">{{ $meeting->meeting_date->translatedFormat('l, d F Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Waktu Acara</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-800">
                                {{ substr($meeting->start_time, 0, 5) }}{{ $meeting->end_time ? ' – ' . substr($meeting->end_time, 0, 5) : '' }} WIB
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 sm:col-span-2">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Ruangan / Tempat</p>
                            <p class="mt-0.5 text-xs font-semibold text-slate-800">{{ $meeting->location }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FORMULIR DINAMIS ============ --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-7">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-[15px] font-bold text-slate-900">Data Kehadiran Peserta</h2>
                        <p class="text-[11px] text-slate-500">
                            @if (isset($participant) && $participant)
                                Terotentikasi sebagai <span class="font-semibold text-slate-800">{{ $participant->name }}</span> (NIP: {{ $participant->nip }}).
                            @elseif ($formFields->isNotEmpty())
                                Isi seluruh data bertanda <span class="font-semibold text-red-500">*</span> sebelum mengirim.
                            @else
                                Belum ada pertanyaan yang dikonfigurasi pada formulir ini.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-7">
                @if (session('error'))
                    <x-alert type="error" :dismissible="true" class="mb-5">{{ session('error') }}</x-alert>
                @endif

                @if ($formFields->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 bg-slate-50/70 p-8 text-center text-slate-500">
                        <svg class="mx-auto h-10 w-10 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-bold text-slate-700">Formulir Masih Kosong</p>
                        <p class="mt-1 text-xs text-slate-500">Panitia belum menambahkan daftar pertanyaan untuk kegiatan ini.</p>
                    </div>
                @else
                    <form
                        method="POST"
                        action="{{ route('absensi.submit', $meeting->public_token) }}"
                        enctype="multipart/form-data"
                        class="space-y-4"
                        x-data="{ submitting: false }"
                        @submit="submitting = true"
                    >
                        @csrf

                        {{-- Loop seluruh pertanyaan dinamis yang disusun Admin di Form Builder --}}
                        @foreach ($formFields as $field)
                            @php
                                $fieldVal = old('fields.' . $field->field_key);
                                // Prefill bila ada sesi login peserta
                                if ($fieldVal === null && isset($participant)) {
                                    $lower = strtolower($field->label . ' ' . $field->field_key);
                                    if (str_contains($lower, 'nip')) {
                                        $fieldVal = $participant->nip;
                                    } elseif (str_contains($lower, 'nama')) {
                                        $fieldVal = $participant->name;
                                    } elseif (str_contains($lower, 'unit') || str_contains($lower, 'instansi')) {
                                        $fieldVal = $participant->unit_kerja;
                                    } elseif (str_contains($lower, 'kabupaten') || str_contains($lower, 'kota')) {
                                        $fieldVal = $participant->kabupaten_kota;
                                    }
                                }
                            @endphp

                            <div>
                                <label for="f_{{ $field->field_key }}" class="block text-xs font-semibold text-slate-800 mb-1">
                                    {{ $field->label }}
                                    @if ($field->is_required)
                                        <span class="text-rose-600 font-bold">*</span>
                                    @endif
                                </label>
                                @if ($field->description)
                                    <p class="text-[11px] text-slate-500 mb-1.5">{{ $field->description }}</p>
                                @endif

                                @if (in_array($field->type, ['text', 'phone', 'email', 'number']))
                                    <input
                                        type="{{ $field->type === 'phone' ? 'tel' : $field->type }}"
                                        id="f_{{ $field->field_key }}"
                                        name="fields[{{ $field->field_key }}]"
                                        value="{{ $fieldVal }}"
                                        placeholder="{{ $field->placeholder ?: ($field->type === 'email' ? 'nama@email.com' : ($field->type === 'phone' ? '08xxxxxxxxxx' : 'Jawaban Anda')) }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                        class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                    >
                                @elseif ($field->type === 'textarea')
                                    <textarea
                                        id="f_{{ $field->field_key }}"
                                        name="fields[{{ $field->field_key }}]"
                                        rows="3"
                                        placeholder="{{ $field->placeholder ?: 'Tuliskan jawaban Anda di sini...' }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                        class="w-full rounded-xl border border-slate-300 bg-white p-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                    >{{ $fieldVal }}</textarea>
                                @elseif ($field->type === 'select')
                                    <x-custom-select
                                        id="f_{{ $field->field_key }}"
                                        name="fields[{{ $field->field_key }}]"
                                        :options="$field->options ?? []"
                                        :value="$fieldVal"
                                        placeholder="-- Pilih salah satu --"
                                        :required="$field->is_required"
                                    />
                                @elseif ($field->type === 'radio')
                                    <div class="space-y-1.5 pt-1">
                                        @foreach ($field->options ?? [] as $opt)
                                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                                                <input
                                                    type="radio"
                                                    name="fields[{{ $field->field_key }}]"
                                                    value="{{ $opt }}"
                                                    {{ $fieldVal === $opt ? 'checked' : '' }}
                                                    {{ $field->is_required ? 'required' : '' }}
                                                    class="h-4 w-4 border-slate-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span>{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($field->type === 'checkbox')
                                    <div class="space-y-1.5 pt-1">
                                        @foreach ($field->options ?? [] as $opt)
                                            <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                                                <input
                                                    type="checkbox"
                                                    name="fields[{{ $field->field_key }}][]"
                                                    value="{{ $opt }}"
                                                    {{ in_array($opt, (array) $fieldVal, true) ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span>{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($field->type === 'scale')
                                    <div class="flex flex-wrap items-center gap-2 pt-1">
                                        @foreach ($field->options ?? ['1', '2', '3', '4', '5'] as $sVal)
                                            <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:border-blue-500 hover:bg-blue-50/50 has-checked:border-blue-600 has-checked:bg-blue-600 has-checked:text-white transition">
                                                <input
                                                    type="radio"
                                                    name="fields[{{ $field->field_key }}]"
                                                    value="{{ $sVal }}"
                                                    {{ $fieldVal === $sVal ? 'checked' : '' }}
                                                    {{ $field->is_required ? 'required' : '' }}
                                                    class="sr-only"
                                                >
                                                <span>{{ $sVal }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @elseif ($field->type === 'file')
                                    <div class="rounded-xl border border-slate-300 bg-white p-3 shadow-2xs">
                                        <input
                                            type="file"
                                            id="f_{{ $field->field_key }}"
                                            name="files[{{ $field->field_key }}]"
                                            accept="image/*,.pdf,.doc,.docx"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                        >
                                    </div>
                                @elseif ($field->type === 'signature')
                                    <div class="rounded-xl border border-slate-300 bg-white p-3 shadow-2xs">
                                        <x-signature-canvas
                                            :name="'fields[' . $field->field_key . ']'"
                                            :label="$field->label"
                                            :required="$field->is_required"
                                        />
                                    </div>
                                @elseif ($field->type === 'date')
                                    <input
                                        type="date"
                                        id="f_{{ $field->field_key }}"
                                        name="fields[{{ $field->field_key }}]"
                                        value="{{ $fieldVal }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                        class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                    >
                                @elseif ($field->type === 'time')
                                    <input
                                        type="time"
                                        id="f_{{ $field->field_key }}"
                                        name="fields[{{ $field->field_key }}]"
                                        value="{{ $fieldVal }}"
                                        {{ $field->is_required ? 'required' : '' }}
                                        class="w-full h-10 rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 shadow-2xs"
                                    >
                                @endif
                            </div>
                        @endforeach

                        {{-- Waktu server otomatis --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-3.5">
                            <div class="mb-2 flex items-center gap-1.5">
                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">Dicatat Otomatis oleh Server</p>
                            </div>
                            <div class="grid grid-cols-1 gap-2.5 text-xs sm:grid-cols-2">
                                <div class="flex justify-between gap-2 rounded-lg bg-white px-3 py-2 border border-slate-200 sm:block">
                                    <span class="text-slate-500">Tanggal</span>
                                    <span class="block font-semibold text-slate-800">{{ now()->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="flex justify-between gap-2 rounded-lg bg-white px-3 py-2 border border-slate-200 sm:block">
                                    <span class="text-slate-500">Waktu (WIB)</span>
                                    <span class="block font-mono font-semibold text-blue-700">{{ now()->format('H:i') }} WIB</span>
                                </div>
                            </div>
                        </div>

                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="submitting"
                                class="inline-flex min-h-[50px] w-full select-none items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-sm font-bold text-white shadow-md shadow-blue-600/25 transition-all duration-150 hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 disabled:cursor-wait disabled:opacity-70"
                            >
                                <svg x-show="submitting" x-cloak class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg x-show="!submitting" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="submitting ? 'Menyimpan kehadiran…' : 'Kirim Absensi'">Kirim Absensi</span>
                            </button>

                            <p class="mt-2.5 text-center text-[11px] leading-relaxed text-slate-500">
                                Dengan mengirim, Anda menyatakan data yang diisi benar dan menyetujui pengolahan data untuk keperluan administrasi sekolah.
                            </p>
                        </div>
                    </form>
                @endif
            </div>
        </section>
    </div>
</x-layouts.guest>
