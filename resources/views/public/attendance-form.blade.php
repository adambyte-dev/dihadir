<x-layouts.guest :title="'Absensi: ' . $meeting->title" max-width="max-w-3xl">
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

            <h1 class="mt-2.5 text-[26px] font-extrabold leading-tight tracking-tight text-slate-900 sm:text-[30px]">
                Absensi Pertemuan
            </h1>
            <p class="mx-auto mt-1.5 max-w-md text-[13px] leading-relaxed text-slate-500">
                Pencatatan kehadiran digital kegiatan resmi di lingkungan SMK Negeri 1 Majalengka.
            </p>
        </header>

        {{-- ============ KARTU IDENTITAS PERTEMUAN ============ --}}
        <section class="relative mb-5 overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="h-1 bg-gradient-to-r from-slate-700 via-blue-600 to-sky-400" aria-hidden="true"></div>

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

                <h2 class="text-[17px] font-bold leading-snug text-slate-900 sm:text-lg">
                    {{ $meeting->title }}
                </h2>

                <div class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2">
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

        {{-- ============ FORMULIR ============ --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl shadow-slate-900/[0.06]">
            <div class="border-b border-slate-100 px-5 py-4 sm:px-7">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-white shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </span>
                    <div>
                        <h2 class="text-[15px] font-bold text-slate-900">Data Kehadiran Peserta</h2>
                        <p class="text-[11px] text-slate-500">Isi seluruh data bertanda <span class="font-semibold text-red-500">*</span> sebelum mengirim.</p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-7">
                @if (session('error'))
                    <x-alert type="error" :dismissible="true" class="mb-5">{{ session('error') }}</x-alert>
                @endif

                <div class="mb-5 flex items-start gap-2.5 rounded-lg border border-blue-200/80 bg-blue-50/70 p-3.5 text-xs leading-relaxed text-blue-900">
                    <svg class="mt-px h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Lengkapi data dengan benar, bubuhkan tanda tangan digital, lalu tekan <strong class="font-semibold">Kirim Data Absensi</strong>. Kehadiran hanya dapat dikirim satu kali per pertemuan.</span>
                </div>

                <form
                    method="POST"
                    action="{{ route('absensi.submit', $meeting->public_token) }}"
                    enctype="multipart/form-data"
                    class="space-y-4"
                    x-data="{ submitting: false }"
                    @submit="submitting = true"
                >
                    @csrf

                    <x-input
                        label="Nama Lengkap & Gelar"
                        name="name"
                        id="name"
                        type="text"
                        value="{{ old('name', $prefilled['name']) }}"
                        placeholder="Contoh: Drs. H. Ahmad Subarkah, M.Pd."
                        :required="true"
                        :error="$errors->first('name')"
                        autocomplete="name"
                    />

                    <x-input
                        label="Nomor Induk Pegawai (NIP)"
                        name="nip"
                        id="nip"
                        type="text"
                        value="{{ old('nip', $prefilled['nip']) }}"
                        placeholder="Contoh: 198501152010011002"
                        :required="true"
                        :error="$errors->first('nip')"
                        helper="NIP menjadi penanda identitas unik agar kehadiran tidak tercatat ganda."
                        inputmode="numeric"
                        autocomplete="off"
                    />

                    <x-input
                        label="Unit Kerja / Instansi Asal"
                        name="unit_kerja"
                        id="unit_kerja"
                        type="text"
                        value="{{ old('unit_kerja', $prefilled['unit_kerja']) }}"
                        placeholder="Contoh: SMKN 1 Majalengka"
                        :required="true"
                        :error="$errors->first('unit_kerja')"
                        autocomplete="organization"
                    />

                    <x-searchable-select
                        name="kabupaten_kota"
                        label="Kabupaten / Kota Domisili Instansi"
                        value="{{ old('kabupaten_kota', $prefilled['kabupaten_kota']) }}"
                        :required="true"
                        :error="$errors->first('kabupaten_kota')"
                    />

                    {{-- Pertanyaan Kustom Tambahan (Form Builder) --}}
                    @if (isset($customFields) && $customFields->isNotEmpty())
                        <div class="my-4 rounded-xl border border-blue-100 bg-blue-50/40 p-4 space-y-4">
                            <div class="border-b border-blue-100 pb-2">
                                <h3 class="text-xs font-bold uppercase tracking-wider text-blue-900">Pertanyaan Tambahan Panitia</h3>
                                <p class="text-[11px] text-blue-700">Mohon lengkapi informasi berikut sesuai arahan panitia.</p>
                            </div>

                            @foreach ($customFields as $field)
                                <div>
                                    <label for="cf_{{ $field->field_key }}" class="block text-xs font-semibold text-slate-800 mb-1">
                                        {{ $field->label }}
                                        @if ($field->is_required)
                                            <span class="text-rose-600 font-bold">*</span>
                                        @endif
                                    </label>
                                    @if ($field->description)
                                        <p class="text-[11px] text-slate-500 mb-1.5">{{ $field->description }}</p>
                                    @endif

                                    @if ($field->type === 'text')
                                        <input
                                            type="text"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            placeholder="{{ $field->placeholder ?: 'Jawaban Anda' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @elseif ($field->type === 'email')
                                        <input
                                            type="email"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            placeholder="{{ $field->placeholder ?: 'nama@email.com' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @elseif ($field->type === 'phone')
                                        <input
                                            type="tel"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            placeholder="{{ $field->placeholder ?: '08xxxxxxxxxx' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @elseif ($field->type === 'textarea')
                                        <textarea
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            rows="3"
                                            placeholder="{{ $field->placeholder ?: 'Tuliskan jawaban Anda di sini...' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full rounded-lg border border-slate-300 bg-white p-2.5 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >{{ old('custom_fields.' . $field->field_key) }}</textarea>
                                    @elseif ($field->type === 'number')
                                        <input
                                            type="number"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            placeholder="{{ $field->placeholder ?: '0' }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @elseif ($field->type === 'select')
                                        <x-custom-select
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            :options="$field->options ?? []"
                                            :value="old('custom_fields.' . $field->field_key)"
                                            placeholder="-- Pilih salah satu --"
                                            :required="$field->is_required"
                                            :error="$errors->first('custom_fields.' . $field->field_key)"
                                        />
                                    @elseif ($field->type === 'radio')
                                        <div class="space-y-1.5 pt-1">
                                            @foreach ($field->options ?? [] as $opt)
                                                <label class="flex items-center gap-2 cursor-pointer text-xs text-slate-700">
                                                    <input
                                                        type="radio"
                                                        id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                                        value="{{ $opt }}"
                                                        {{ old('custom_fields.' . $field->field_key) === $opt ? 'checked' : '' }}
                                                        {{ $field->is_required ? 'required' : '' }}
                                                        class="h-3.5 w-3.5 border-slate-300 text-blue-600 focus:ring-blue-500"
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
                                                        name="custom_fields[{{ $field->field_key }}][]"
                                                        value="{{ $opt }}"
                                                        {{ in_array($opt, (array) old('custom_fields.' . $field->field_key, []), true) ? 'checked' : '' }}
                                                        class="h-3.5 w-3.5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                                    >
                                                    <span>{{ $opt }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($field->type === 'scale')
                                        <div class="flex flex-wrap items-center gap-2 pt-1.5">
                                            @foreach ($field->options ?? ['1', '2', '3', '4', '5'] as $sVal)
                                                <label class="flex cursor-pointer items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-2xs hover:border-blue-500 hover:bg-blue-50/50 has-checked:border-blue-600 has-checked:bg-blue-600 has-checked:text-white transition">
                                                    <input
                                                        type="radio"
                                                        id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                                        value="{{ $sVal }}"
                                                        {{ old('custom_fields.' . $field->field_key) === $sVal ? 'checked' : '' }}
                                                        {{ $field->is_required ? 'required' : '' }}
                                                        class="sr-only"
                                                    >
                                                    <span>{{ $sVal }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif ($field->type === 'file')
                                        <div class="rounded-lg border border-slate-300 bg-white p-3">
                                            <input
                                                type="file"
                                                id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_files[{{ $field->field_key }}]"
                                                accept="image/*,.pdf,.doc,.docx"
                                                {{ $field->is_required ? 'required' : '' }}
                                                class="block w-full text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                                            >
                                            <p class="mt-1 text-[10.5px] text-slate-400">Format gambar JPG/PNG akan dikompresi otomatis oleh server.</p>
                                        </div>
                                    @elseif ($field->type === 'signature')
                                        <div class="rounded-lg border border-slate-300 bg-white p-3">
                                            <x-signature-canvas
                                                :name="'custom_fields[' . $field->field_key . ']'"
                                                :label="$field->label"
                                                :required="$field->is_required"
                                                :error="$errors->first('custom_fields.' . $field->field_key)"
                                            />
                                        </div>
                                    @elseif ($field->type === 'date')
                                        <input
                                            type="date"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @elseif ($field->type === 'time')
                                        <input
                                            type="time"
                                            id="cf_{{ $field->field_key }}" id="cf_{{ $field->field_key }}" name="custom_fields[{{ $field->field_key }}]"
                                            value="{{ old('custom_fields.' . $field->field_key) }}"
                                            {{ $field->is_required ? 'required' : '' }}
                                            class="w-full h-9 rounded-lg border border-slate-300 bg-white px-3 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                        >
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Waktu server --}}
                    <div class="rounded-lg border border-slate-200 bg-slate-50/80 p-3.5">
                        <div class="mb-2 flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">Dicatat Otomatis oleh Server</p>
                        </div>
                        <div class="grid grid-cols-1 gap-2.5 text-xs sm:grid-cols-2">
                            <div class="flex justify-between gap-2 rounded-md bg-white px-2.5 py-2 ring-1 ring-inset ring-slate-200 sm:block">
                                <span class="text-slate-500">Tanggal</span>
                                <span class="block font-semibold text-slate-800">{{ now()->translatedFormat('d F Y') }}</span>
                            </div>
                            <div class="flex justify-between gap-2 rounded-md bg-white px-2.5 py-2 ring-1 ring-inset ring-slate-200 sm:block">
                                <span class="text-slate-500">Waktu (WIB)</span>
                                <span class="block font-mono font-semibold text-blue-700">{{ now()->format('H:i') }} WIB</span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-1">
                        <x-signature-canvas
                            name="signature"
                            label="Tanda Tangan Digital"
                            :required="true"
                            :error="$errors->first('signature')"
                        />
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
            </div>
        </section>
    </div>
</x-layouts.guest>
