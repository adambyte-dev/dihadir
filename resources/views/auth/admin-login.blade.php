<x-layouts.guest title="Masuk Panel Admin" max-width="max-w-[1180px]">
    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,440px)] lg:gap-8">

        {{-- ============ PANEL IDENTITAS (kiri, desktop) ============ --}}
        <section class="relative hidden overflow-hidden rounded-2xl bg-slate-900 border border-slate-800 p-8 text-white shadow-sm lg:flex lg:flex-col">
            {{-- Ornamen garis pemisah halus --}}
            <div class="pointer-events-none absolute inset-0 opacity-[0.25]" aria-hidden="true">
                <div class="absolute inset-x-0 bottom-0 h-px bg-slate-800"></div>
            </div>

            <div class="relative">
                <div class="inline-flex items-center gap-3 rounded-xl bg-white/95 px-4 py-3 shadow-lg shadow-slate-950/30 ring-1 ring-white/20">
                    <img
                        src="{{ asset('images/logo-dihadir-full-trimmed.webp') }}"
                        alt="DIHADIR"
                        width="1623" height="400"
                        class="h-10 w-auto object-contain"
                    >
                </div>

                <div class="mt-8 flex items-start gap-4">
                    <div class="shrink-0 rounded-xl bg-white/95 p-2 shadow-md">
                        <img
                            src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}"
                            alt="Lambang SMK Negeri 1 Majalengka"
                            width="1196" height="1127"
                            class="h-16 w-16 object-contain"
                        >
                    </div>
                    <div class="pt-1">
                        <p class="text-[10px] font-bold uppercase tracking-[0.16em] text-blue-300">Sistem Resmi Sekolah</p>
                        <p class="mt-1 text-lg font-bold leading-tight text-white">SMK Negeri 1<br>Majalengka</p>
                    </div>
                </div>

                <h2 class="mt-8 max-w-sm text-2xl font-extrabold leading-snug tracking-tight text-white">
                    Presensi pertemuan yang rapi, terukur, dan siap dicetak.
                </h2>
                <p class="mt-3 max-w-sm text-sm leading-relaxed text-slate-300">
                    Kelola agenda kegiatan, terbitkan formulir kehadiran digital, dan hasilkan berita acara resmi ber-KOP sekolah dalam satu panel.
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
                        <p class="text-[13px] font-semibold text-white">Anti-Duplikasi Presensi</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">Satu peserta hanya dapat mengisi satu kali per pertemuan.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl bg-white/[0.05] p-3.5 ring-1 ring-white/10">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Waktu Server Asia/Jakarta</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">Timestamp kehadiran diambil dari server, bukan perangkat peserta.</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 rounded-xl bg-white/[0.05] p-3.5 ring-1 ring-white/10">
                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-amber-500/20 text-amber-300">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    <div>
                        <p class="text-[13px] font-semibold text-white">Laporan PDF, CSV &amp; Cetak</p>
                        <p class="mt-0.5 text-[11px] leading-relaxed text-slate-300">Rekap kehadiran siap diunduh dan ditandatangani kepala sekolah.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ============ FORM LOGIN ============ --}}
        <section class="mx-auto w-full max-w-md lg:mx-0">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="h-1 bg-blue-600" aria-hidden="true"></div>

                <div class="p-6 sm:p-8">
                    <div class="mb-6">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-slate-100 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-slate-600 ring-1 ring-inset ring-slate-200">
                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Akses Terbatas
                        </span>

                        <h1 class="mt-3 text-[26px] font-extrabold leading-tight tracking-tight text-slate-900">
                            Masuk Panel Admin
                        </h1>
                        <p class="mt-1.5 text-[13px] leading-relaxed text-slate-500">
                            Khusus panitia dan staf administrator SMKN 1 Majalengka. Gunakan akun yang telah diberikan.
                        </p>
                    </div>

                    @if (session('error'))
                        <x-alert type="error" :dismissible="true" class="mb-4">{{ session('error') }}</x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert type="info" :dismissible="true" class="mb-4">{{ session('info') }}</x-alert>
                    @endif
                    @if (session('success'))
                        <x-alert type="success" :dismissible="true" class="mb-4">{{ session('success') }}</x-alert>
                    @endif

                    <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4" x-data="{ showPassword: false, isFormVerified: false, submitting: false }" @verified="isFormVerified = true" @submit="submitting = true">
                        @csrf

                        <x-input
                            label="Email atau NIP Pengelola"
                            name="login"
                            id="login"
                            type="text"
                            value="{{ old('login') }}"
                            placeholder="admin@dihadir.wacanateknologi.id"
                            :required="true"
                            :error="$errors->first('login')"
                            autocomplete="username"
                            autofocus
                        />

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

                        {{-- Verifikasi Keamanan Puzzle Captcha --}}
                        <div class="pt-1">
                            <x-slide-to-verify name="slider_token" />
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-0.5">
                            <label class="inline-flex cursor-pointer select-none items-center gap-2">
                                <input
                                    id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500 focus:ring-offset-0"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span class="text-xs font-medium text-slate-600">Ingat saya di perangkat ini</span>
                            </label>

                            <a href="{{ route('absensi.login') }}" class="text-xs font-semibold text-blue-600 transition-colors hover:text-blue-800">
                                Login Absensi
                            </a>
                        </div>

                        <div class="pt-2">
                            <button
                                type="submit"
                                x-bind:disabled="!isFormVerified || submitting"
                                class="inline-flex min-h-[48px] w-full items-center justify-center gap-2 rounded-xl px-5 text-sm font-bold transition-all duration-150 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 select-none"
                                :class="isFormVerified
                                    ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 hover:bg-blue-700'
                                    : 'bg-slate-200 text-slate-500 cursor-not-allowed'"
                            >
                                <svg x-show="submitting" x-cloak class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-90" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                </svg>
                                <svg x-show="!submitting" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                </svg>
                                <span x-text="submitting ? 'Memverifikasi…' : 'Masuk ke Panel Admin'">Masuk ke Panel Admin</span>
                            </button>

                            <p class="mt-2 text-center text-[11px] text-slate-500" x-show="!isFormVerified" x-cloak>
                                Geser verifikasi keamanan di atas terlebih dahulu untuk mengaktifkan tombol masuk.
                            </p>
                        </div>
                    </form>
                </div>

                <div class="border-t border-slate-100 bg-slate-50/80 px-6 py-3.5 sm:px-8">
                    <p class="text-center text-[11px] leading-relaxed text-slate-500">
                        Lupa kata sandi? Hubungi <span class="font-semibold text-slate-700">IT Support</span> SMKN 1 Majalengka untuk pengaturan ulang.
                    </p>
                </div>
            </div>

            <p class="mt-4 text-center text-[11px] text-slate-500">
                Sesi Anda dilindungi CSRF token &amp; rate limiting login.
            </p>
        </section>
    </div>
</x-layouts.guest>
