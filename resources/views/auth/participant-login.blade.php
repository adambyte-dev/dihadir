<x-layouts.guest title="Form Login Absensi" max-width="max-w-[1180px]">
    <div
        class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,440px)] lg:gap-8"
        x-data="{ isFormVerified: false, showPassword: false, showHelpModal: false }"
    >
        {{-- ============ PANEL INFORMASI (kiri, desktop) ============ --}}
        <section class="relative hidden overflow-hidden rounded-2xl bg-gradient-to-br from-[#1E293B] via-[#172033] to-[#0F172A] p-8 text-white shadow-xl lg:flex lg:flex-col">
            <div class="pointer-events-none absolute inset-0 opacity-[0.35]" aria-hidden="true">
                <div class="absolute -right-24 -top-24 h-72 w-72 rounded-full border border-white/10"></div>
                <div class="absolute -right-10 top-24 h-48 w-48 rounded-full border border-white/[0.07]"></div>
                <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-blue-400/40 to-transparent"></div>
            </div>

            <div class="relative">
                <div class="inline-flex items-center gap-3 rounded-xl bg-white/95 px-4 py-3 shadow-lg shadow-slate-950/30 ring-1 ring-white/20">
                    <img
                        src="{{ asset('images/logo-dihadir-full-trimmed.png') }}"
                        alt="DIHADIR"
                        width="1623" height="400"
                        class="h-10 w-auto object-contain"
                    >
                </div>

                <div class="mt-8 flex items-start gap-4">
                    <div class="shrink-0 rounded-xl bg-white/95 p-2 shadow-md">
                        <img
                            src="{{ asset('images/logo-smkn1majalengka-trimmed.png') }}"
                            alt="Lambang SMK Negeri 1 Majalengka"
                            width="1196" height="1127"
                            class="h-16 w-16 object-contain"
                        >
                    </div>
                    <div class="pt-1">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-blue-300">Formulir Kehadiran</p>
                        <p class="mt-1 text-lg font-bold leading-tight text-white">SMK Negeri 1<br>Majalengka</p>
                    </div>
                </div>

                <h2 class="mt-8 max-w-sm text-2xl font-extrabold leading-snug tracking-tight text-white">
                    Masuk untuk mencatat kehadiran Anda pada pertemuan.
                </h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-300">
                    Gunakan NIP dan kata sandi yang telah terdaftar. Kehadiran Anda langsung tercatat dengan waktu server dan tanda tangan digital.
                </p>
            </div>

            <div class="relative mt-10 space-y-3">
                <div class="flex items-start gap-3 rounded-xl bg-white/[0.05] p-3.5 ring-1 ring-white/10">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-500/20 text-blue-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Cukup Sekali Isi</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">Sistem menolak pengisian ganda pada pertemuan yang sama.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl bg-white/[0.05] p-3.5 ring-1 ring-white/10">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Tanda Tangan di Layar</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">Bubuhkan tanda tangan langsung dengan jari atau pointer.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl bg-white/[0.05] p-3.5 ring-1 ring-white/10">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-500/20 text-amber-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Data Terlindungi</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">NIP dan tanda tangan hanya dapat diakses pengelola berwenang.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FORM ============ --}}
        <section class="mx-auto w-full max-w-md lg:mx-0">
            {{-- Banner pertemuan tertaut --}}
            @if ($meeting)
                <div class="mb-4 overflow-hidden rounded-xl border border-blue-200/90 bg-white shadow-sm">
                    <div class="h-[3px] bg-blue-500" aria-hidden="true"></div>
                    <div class="flex items-start gap-3 p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-blue-700">Kegiatan yang Anda ikuti</p>
                            <p class="mt-1 text-[13px] font-bold leading-snug text-slate-900">{{ $meeting->title }}</p>
                            <p class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-slate-500">
                                <span>{{ $meeting->meeting_date->translatedFormat('d M Y') }}</span>
                                <span class="text-slate-300">•</span>
                                <span class="truncate">{{ $meeting->location }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-xl shadow-slate-900/[0.07]">
                <div class="h-1 bg-gradient-to-r from-slate-700 via-blue-600 to-sky-400" aria-hidden="true"></div>

                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-blue-50 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-blue-700 ring-1 ring-inset ring-blue-200">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Akses Peserta
                        </span>

                        <h1 class="mt-3 text-[26px] font-extrabold leading-tight tracking-tight text-slate-900">
                            Masuk Absensi Pertemuan
                        </h1>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-slate-500">
                            Masukkan NIP dan kata sandi terdaftar untuk membuka formulir kehadiran.
                        </p>
                    </div>

                    @if (session('error'))
                        <x-alert type="error" :dismissible="true" class="mb-4">{{ session('error') }}</x-alert>
                    @endif
                    @if (session('success'))
                        <x-alert type="success" :dismissible="true" class="mb-4">{{ session('success') }}</x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert type="info" :dismissible="true" class="mb-4">{{ session('info') }}</x-alert>
                    @endif

                    <form method="POST" action="{{ route('absensi.login.submit') }}" class="space-y-4" @verified="isFormVerified = true">
                        @csrf

                        @if ($meeting)
                            <input type="hidden" name="meeting_token" value="{{ $meeting->public_token }}">
                        @endif

                        {{-- NIP --}}
                        <div>
                            <label for="nip" class="mb-1.5 block text-[13px] font-semibold text-slate-700">
                                NIP (Nomor Induk Pegawai) <span class="ml-0.5 text-red-500" aria-hidden="true">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="nip"
                                    name="nip"
                                    value="{{ old('nip') }}"
                                    placeholder="Contoh: 198501152010011002"
                                    required
                                    inputmode="numeric"
                                    autocomplete="username"
                                    autofocus
                                    class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 shadow-sm transition duration-150 placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 @error('nip') border-red-400 @enderror"
                                >
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500">
                                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                </span>
                            </div>
                            @error('nip')
                                <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-red-600">
                                    <svg class="mt-px h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div>
                            <div class="mb-1.5 flex items-center justify-between gap-2">
                                <label for="password" class="block text-[13px] font-semibold text-slate-700">
                                    Kata Sandi <span class="ml-0.5 text-red-500" aria-hidden="true">*</span>
                                </label>
                                <button type="button" @click="showPassword = !showPassword" class="text-[11px] font-semibold text-blue-600 transition-colors hover:text-blue-800">
                                    <span x-text="showPassword ? 'Sembunyikan' : 'Tampilkan'">Tampilkan</span>
                                </button>
                            </div>

                            <div class="relative">
                                <input
                                    :type="showPassword ? 'text' : 'password'"
                                    id="password"
                                    name="password"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password"
                                    class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-3.5 pr-11 text-sm text-slate-900 shadow-sm transition duration-150 placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 @error('password') border-red-400 @enderror"
                                >
                                <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-500">
                                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                            </div>
                            @error('password')
                                <p class="mt-1.5 flex items-start gap-1 text-xs font-medium text-red-600">
                                    <svg class="mt-px h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        {{-- Verifikasi geser --}}
                        <div class="pt-1">
                            <x-slide-to-verify name="slider_token" />
                        </div>

                        {{-- Tombol login --}}
                        <div class="pt-2">
                            <button
                                type="submit"
                                :disabled="!isFormVerified"
                                class="inline-flex min-h-[48px] w-full items-center justify-center gap-2 rounded-xl px-5 text-sm font-bold transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 select-none"
                                :class="isFormVerified
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 hover:bg-blue-700'
                                    : 'bg-slate-200 text-slate-500 cursor-not-allowed'"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span>Masuk ke Formulir Kehadiran</span>
                            </button>

                            <p class="mt-2 text-center text-[11px] text-slate-500" x-show="!isFormVerified" x-cloak>
                                Geser verifikasi di atas terlebih dahulu untuk mengaktifkan tombol.
                            </p>
                        </div>
                    </form>

                    <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                        <button
                            type="button"
                            @click="showHelpModal = true"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 transition-colors hover:text-blue-600"
                        >
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Lupa kata sandi atau NIP belum terdaftar?</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between gap-2">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-500 transition-colors hover:text-blue-600">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>
                <a href="{{ route('admin.login') }}" class="text-[11px] font-semibold text-slate-500 transition-colors hover:text-blue-600">
                    Login Admin
                </a>
            </div>
        </section>

        {{-- ============ MODAL BANTUAN ============ --}}
        <div
            x-show="showHelpModal"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @keydown.escape.window="showHelpModal = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="help-modal-title"
        >
            <div class="w-full max-w-sm overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl" @click.away="showHelpModal = false">
                <div class="h-1 bg-gradient-to-r from-slate-700 via-blue-600 to-sky-400" aria-hidden="true"></div>
                <div class="p-6">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 id="help-modal-title" class="text-[15px] font-bold text-slate-900">Bantuan Akses Absensi</h3>
                            <p class="mt-1 text-xs leading-relaxed text-slate-600">
                                Jika lupa kata sandi atau NIP belum terdaftar, hubungi tim administrasi sekolah.
                            </p>
                        </div>
                    </div>

                    <dl class="mt-4 space-y-2 rounded-xl border border-slate-200 bg-slate-50/80 p-3.5 text-xs">
                        <div class="flex justify-between gap-3">
                            <dt class="font-medium text-slate-500">Layanan</dt>
                            <dd class="text-right font-semibold text-slate-800">IT Support Sekolah</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="font-medium text-slate-500">Email</dt>
                            <dd class="text-right font-semibold text-slate-800">admin@smkn1majalengka.sch.id</dd>
                        </div>
                        <div class="flex justify-between gap-3">
                            <dt class="font-medium text-slate-500">Lokasi</dt>
                            <dd class="text-right font-semibold text-slate-800">Ruang Panitia</dd>
                        </div>
                    </dl>

                    <div class="mt-5">
                        <button
                            type="button"
                            class="w-full rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50"
                            @click="showHelpModal = false"
                        >
                            Tutup Panduan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
