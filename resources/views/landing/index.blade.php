<x-layouts.landing
    title="Sistem Presensi Digital Pertemuan Resmi SMKN 1 Majalengka"
    description="DIHADIR adalah sistem pengelolaan presensi digital resmi SMKN 1 Majalengka. Pengisian absensi cepat lewat QR Code, tanda tangan touchscreen, anti-duplikasi, dan rekapitulasi laporan berstandar kedinasan."
    :canonical="route('landing')"
>
    {{-- Structured Data (Schema.org JSON-LD): WebSite, SoftwareApplication, Organization --}}
    @push('head')
    @php
        $schemaData = [
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'EducationalOrganization',
                    '@id' => 'https://smkn1majalengka.sch.id/#organization',
                    'name' => 'SMK Negeri 1 Majalengka',
                    'url' => 'https://smkn1majalengka.sch.id',
                    'logo' => asset('images/logo-smkn1majalengka.png'),
                    'address' => [
                        '@type' => 'PostalAddress',
                        'streetAddress' => 'Jl. Raya Tonjong–Pinangraja No. 55',
                        'addressLocality' => 'Majalengka',
                        'addressRegion' => 'Jawa Barat',
                        'postalCode' => '45413',
                        'addressCountry' => 'ID',
                    ],
                    'telephone' => '+62-233-282913',
                    'email' => 'info@smkn1majalengka.sch.id',
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => route('landing') . '#website',
                    'url' => route('landing'),
                    'name' => 'DIHADIR — Sistem Presensi Digital Pertemuan Resmi',
                    'description' => 'Sistem Presensi Digital dan Manajemen Daftar Hadir Pertemuan Resmi SMKN 1 Majalengka',
                    'publisher' => [
                        '@id' => 'https://smkn1majalengka.sch.id/#organization',
                    ],
                    'inLanguage' => 'id-ID',
                ],
                [
                    '@type' => 'SoftwareApplication',
                    '@id' => route('landing') . '#application',
                    'name' => 'DIHADIR',
                    'applicationCategory' => 'BusinessApplication',
                    'operatingSystem' => 'Web Browser',
                    'offers' => [
                        '@type' => 'Offer',
                        'price' => '0',
                        'priceCurrency' => 'IDR',
                    ],
                    'description' => 'Aplikasi manajemen presensi digital rapat, pertemuan orang tua, dewan guru, dan kemitraan industri SMKN 1 Majalengka dengan QR code instan dan tanda tangan digital.',
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush
    {{-- ==================== 1. HERO SECTION ==================== --}}
    <section class="relative overflow-hidden border-b border-slate-200/80 bg-white py-16 sm:py-24">
        <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                {{-- Left Text --}}
                <div class="space-y-6 text-center lg:col-span-7 lg:text-left animate-fade-in-up">
                    {{-- Main Headline --}}
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 sm:text-5xl sm:leading-[1.15] lg:text-5xl">
                        Presensi Digital Akuntabel untuk Kegiatan & Pertemuan Sekolah
                    </h1>

                    <p class="mx-auto max-w-2xl text-sm leading-relaxed text-slate-600 sm:text-base lg:mx-0">
                        DIHADIR menggantikan daftar hadir kertas konvensional dengan formulir presensi terstruktur, QR Code instan, penandatanganan digital langsung di layar, dan rekapitulasi laporan berstandar kedinasan.
                    </p>

                    {{-- CTA Group --}}
                    <div class="flex flex-col items-center justify-center gap-3 sm:flex-row lg:justify-start">
                        <a
                            href="{{ route('absensi.login') }}"
                            class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Isi Kehadiran Peserta</span>
                        </a>

                        <a
                            href="{{ route('admin.login') }}"
                            class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-2xs transition hover:bg-slate-50 hover:text-slate-900 sm:w-auto"
                        >
                            <svg class="h-4 w-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Panel Masuk Admin</span>
                        </a>
                    </div>

                    {{-- Trust points --}}
                    <div class="grid grid-cols-2 gap-4 border-t border-slate-200 pt-6 sm:grid-cols-3">
                        <div class="space-y-1 text-center sm:text-left">
                            <p class="text-xl font-bold text-slate-900">100%</p>
                            <p class="text-xs text-slate-500">Timestamp Server Resmi (WIB)</p>
                        </div>
                        <div class="space-y-1 text-center sm:text-left">
                            <p class="text-xl font-bold text-slate-900">Anti-Duplikasi</p>
                            <p class="text-xs text-slate-500">Validasi NIP & Kunci Token Unik</p>
                        </div>
                        <div class="col-span-2 space-y-1 text-center sm:col-span-1 sm:text-left">
                            <p class="text-xl font-bold text-slate-900">Ekspor Siap Cetak</p>
                            <p class="text-xs text-slate-500">PDF & CSV Rekap Kearsipan</p>
                        </div>
                    </div>
                </div>

                {{-- Right Interactive Preview Card --}}
                <div class="lg:col-span-5 animate-fade-in-up animation-delay-200">
                    <div class="relative mx-auto max-w-md rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xl shadow-slate-200/60 ring-1 ring-slate-900/5 sm:p-8">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Pratinjau Presensi</h3>
                                    <p class="text-xs text-slate-500">Format Resmi Pertemuan Sekolah</p>
                                </div>
                            </div>
                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-800">
                                Aktif (Terbit)
                            </span>
                        </div>

                        {{-- Mockup Form --}}
                        <div class="mt-5 space-y-4 text-xs text-slate-600">
                            <div class="rounded-xl border border-slate-100 bg-slate-50/70 p-3">
                                <span class="font-semibold text-slate-700">Nama Pertemuan:</span>
                                <p class="mt-0.5 font-medium text-slate-900">Rapat Pleno Komite & Orang Tua Siswa</p>
                                <p class="text-[11px] text-slate-500">SMK Negeri 1 Majalengka · Graha Utama</p>
                            </div>

                            <div class="space-y-1">
                                <label class="font-semibold text-slate-700">Nomor Induk Pegawai (NIP)</label>
                                <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-white px-3 py-2 text-slate-800">
                                    <span>198501152010011002</span>
                                    <svg class="h-4 w-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="font-semibold text-slate-700">Tanda Tangan Digital</label>
                                <div class="relative h-20 rounded-lg border border-dashed border-slate-300 bg-slate-50/50 p-2 text-center flex items-center justify-center">
                                    <svg class="h-8 w-24 text-blue-600" viewBox="0 0 100 40" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M10,25 C25,5 30,35 45,15 C55,2 60,30 75,20 C85,15 90,28 95,22" />
                                    </svg>
                                    <span class="absolute bottom-1 right-2 text-[9px] font-mono text-slate-400">Terverifikasi Canvas</span>
                                </div>
                            </div>

                            <div class="rounded-lg bg-blue-50/70 p-2.5 text-[11px] text-blue-800">
                                ⏱ Waktu Rekam: <strong>{{ date('d F Y, H:i') }} WIB</strong> (Server Timestamp)
                            </div>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] text-slate-400">Keamanan enkripsi SHA-256</span>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600">
                                Validasi Otomatis
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 2. FITUR UNGGULAN ==================== --}}
    <section id="fitur" class="py-16 sm:py-24 bg-white border-b border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Keunggulan Sistem</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Dirancang Khusus untuk Standar Birokrasi & Sekolah Modern
                </h2>
                <p class="text-sm sm:text-base text-slate-600">
                    DIHADIR menyederhanakan seluruh alur absensi kegiatan tanpa mengorbankan integritas data dan kepatuhan administrasi.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Fitur 1 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up">
                    <div class="h-12 w-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">QR Code & Tautan Akses Instan</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Setiap acara memiliki kode QR unik siap cetak yang dapat dipajang di pintu masuk aula/ruangan atau dibagikan melalui WhatsApp grup kepanitiaan.
                    </p>
                </div>

                {{-- Fitur 2 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up animation-delay-100">
                    <div class="h-12 w-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Tanda Tangan Digital Touchscreen</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Peserta menandatangani kehadiran langsung di layar ponsel atau laptop dengan kompresi gambar PNG otomatis, tanpa membutuhkan pena atau kertas fisik.
                    </p>
                </div>

                {{-- Fitur 3 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up animation-delay-200">
                    <div class="h-12 w-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Pencegahan Presensi Ganda</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sistem memverifikasi NIP peserta dan sesi browser untuk mencegah pengisian berulang kali pada kegiatan pertemuan yang sama.
                    </p>
                </div>

                {{-- Fitur 4 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up animation-delay-100">
                    <div class="h-12 w-12 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Custom Form Builder Dinamis</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Panitia bebas menyusun pertanyaan tambahan (pilihan ganda, teks, unggah berkas surat tugas, skala linear) dengan kemudahan drag-and-order.
                    </p>
                </div>

                {{-- Fitur 5 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up animation-delay-200">
                    <div class="h-12 w-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Timestamp Server Resmi (WIB)</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Waktu kehadiran dikunci dari jam server Jakarta (WIB), tidak dapat dimanipulasi oleh perubahan jam lokal pada perangkat pengguna.
                    </p>
                </div>

                {{-- Fitur 6 --}}
                <div class="rounded-2xl border border-slate-200/90 bg-slate-50/50 p-7 transition hover:border-blue-300 hover:bg-white hover:shadow-md animate-fade-in-up animation-delay-300">
                    <div class="h-12 w-12 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center font-bold mb-5">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Rekapitulasi PDF & CSV Kedinasan</h3>
                    <p class="mt-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sekali klik untuk mengunduh rekap berformat KOP resmi SMKN 1 Majalengka, lengkap dengan spesimen tanda tangan untuk arsip SPJ atau laporan BOS.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 3. ALUR KERJA ==================== --}}
    <section id="alur" class="py-16 sm:py-24 bg-slate-50 border-b border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Alur Sederhana</span>
                <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Bagaimana DIHADIR Bekerja?
                </h2>
                <p class="text-sm sm:text-base text-slate-600">
                    Proses terstruktur yang mempermudah panitia dan peserta dalam hitungan detik.
                </p>
            </div>

            <div class="mt-14 grid grid-cols-1 md:grid-cols-4 gap-6">
                {{-- Langkah 1 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-2xl font-black text-blue-600 font-mono">01</span>
                        <h3 class="mt-3 text-sm font-bold text-slate-900">Admin Buat Acara</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Pengelola memasukkan nama pertemuan, lokasi, batas waktu kehadiran, serta kustomisasi pertanyaan formulir jika diperlukan.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-slate-400">
                        Dashboard Pengelola
                    </div>
                </div>

                {{-- Langkah 2 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-2xl font-black text-blue-600 font-mono">02</span>
                        <h3 class="mt-3 text-sm font-bold text-slate-900">Terbitkan QR & Link</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Sistem secara instan mengunci formulir, menghasilkan tautan aman publik dan QR Code berkualitas tinggi yang siap dicetak/ditayangkan.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-slate-400">
                        Otomatisasi Sistem
                    </div>
                </div>

                {{-- Langkah 3 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-2xl font-black text-blue-600 font-mono">03</span>
                        <h3 class="mt-3 text-sm font-bold text-slate-900">Peserta Isi & Tanda Tangan</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Peserta memindai QR atau membuka tautan, mengisi NIP, unit kerja, tanda tangan digital di layar, lalu mengirim presensi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-slate-400">
                        Mobile-Friendly
                    </div>
                </div>

                {{-- Langkah 4 --}}
                <div class="relative bg-white rounded-2xl p-6 border border-slate-200 shadow-xs flex flex-col justify-between">
                    <div>
                        <span class="text-2xl font-black text-blue-600 font-mono">04</span>
                        <h3 class="mt-3 text-sm font-bold text-slate-900">Rekap & Laporan Sah</h3>
                        <p class="mt-2 text-xs text-slate-500 leading-relaxed">
                            Data langsung terangkum rapi di panel admin, siap diekspor ke format PDF bertanda tangan dan CSV untuk pelaporan instansi.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 text-[11px] font-semibold text-slate-400">
                        Kearsipan Kedinasan
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 4. TENTANG TIM PENGEMBANG & INOVASI ==================== --}}
    <section id="pengembang" class="py-12 sm:py-16 bg-white border-b border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Header Ringkas --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-6 pb-4 border-b border-slate-100">
                <div>
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider text-blue-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600"></span>
                        Inovasi Civitas SMKN 1 Majalengka
                    </span>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                        Tim Pengembang &amp; Inisiator Sistem
                    </h2>
                </div>
                <div class="inline-flex items-center gap-2 rounded-lg bg-slate-50 border border-slate-200 px-3 py-1 text-xs text-slate-600 shrink-0">
                    <span class="font-medium">TIM IT Perpustakaan</span>
                    <span class="text-slate-300">•</span>
                    <span class="font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/60 px-1.5 py-0.2 rounded">SRJ-LIB-9E4B21</span>
                </div>
            </div>

            {{-- 4 Kartu Tim Pengembang (Desain Elegan, Jelas & Berbobot) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- 1. Muhamad Adam Hidayat --}}
                <div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/50">
                    <div>
                        {{-- Avatar & Badge ID --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="relative">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl border-2 border-slate-200 bg-slate-50 p-0.5 shadow-sm ring-4 ring-slate-100">
                                    <img
                                        src="{{ asset('images/team-adam.jpg') }}"
                                        alt="Foto Muhamad Adam Hidayat - Programmer Utama DIHADIR"
                                        width="320" height="320"
                                        loading="lazy"
                                        class="h-full w-full rounded-[14px] object-cover object-top"
                                    >
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                SRJ-DEV-4E82A1
                            </span>
                        </div>

                        {{-- Nama & Jabatan --}}
                        <div class="mt-4">
                            <h3 class="text-[17px] font-extrabold tracking-tight text-slate-900">
                                Muhamad Adam Hidayat
                            </h3>
                            <p class="mt-1 text-xs font-bold text-slate-700">
                                Programmer Utama &amp; Teknis Sistem
                            </p>
                        </div>

                        {{-- Uraian Tugas --}}
                        <div class="mt-4 border-t border-slate-100 pt-3.5">
                            <p class="text-[12.5px] leading-relaxed text-slate-600">
                                Perancang arsitektur sistem, penanggung jawab teknis, kode dari nol &amp; database relasional.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 2. Keisha Nur Asyifa --}}
                <div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/50">
                    <div>
                        {{-- Avatar & Badge ID --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="relative">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl border-2 border-slate-200 bg-slate-50 p-0.5 shadow-sm ring-4 ring-slate-100">
                                    <img
                                        src="{{ asset('images/team-keisha.jpg') }}"
                                        alt="Foto Keisha Nur Asyifa - Lead QA DIHADIR"
                                        width="320" height="320"
                                        loading="lazy"
                                        class="h-full w-full rounded-[14px] object-cover object-top"
                                    >
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                QA Lead
                            </span>
                        </div>

                        {{-- Nama & Jabatan --}}
                        <div class="mt-4">
                            <h3 class="text-[17px] font-extrabold tracking-tight text-slate-900">
                                Keisha Nur Asyifa
                            </h3>
                            <p class="mt-1 text-xs font-bold text-slate-700">
                                Lead QA &amp; Non-Teknis
                            </p>
                        </div>

                        {{-- Uraian Tugas --}}
                        <div class="mt-4 border-t border-slate-100 pt-3.5">
                            <p class="text-[12.5px] leading-relaxed text-slate-600">
                                Penguji alur fitur presensi &amp; validasi kebutuhan operasional.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 3. Aldi Faturrizqy --}}
                <div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/50">
                    <div>
                        {{-- Avatar & Badge ID --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="relative">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl border-2 border-slate-200 bg-slate-50 p-0.5 shadow-sm ring-4 ring-slate-100">
                                    <img
                                        src="{{ asset('images/team-aldi.jpg') }}"
                                        alt="Foto Aldi Faturrizqy - Tim Dukungan & Presentasi DIHADIR"
                                        width="320" height="320"
                                        loading="lazy"
                                        class="h-full w-full rounded-[14px] object-cover object-top"
                                    >
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                Support
                            </span>
                        </div>

                        {{-- Nama & Jabatan --}}
                        <div class="mt-4">
                            <h3 class="text-[17px] font-extrabold tracking-tight text-slate-900">
                                Aldi Faturrizqy
                            </h3>
                            <p class="mt-1 text-xs font-bold text-slate-700">
                                Dukungan &amp; Presentasi
                            </p>
                        </div>

                        {{-- Uraian Tugas --}}
                        <div class="mt-4 border-t border-slate-100 pt-3.5">
                            <p class="text-[12.5px] leading-relaxed text-slate-600">
                                Uji kompatibilitas multi-ponsel &amp; kesiapan panitia sekolah.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- 4. Nayma Zahra Nurrizki --}}
                <div class="group flex flex-col justify-between rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm transition hover:border-slate-300 hover:shadow-lg hover:shadow-slate-200/50">
                    <div>
                        {{-- Avatar & Badge ID --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="relative">
                                <div class="h-20 w-20 overflow-hidden rounded-2xl border-2 border-slate-200 bg-slate-50 p-0.5 shadow-sm ring-4 ring-slate-100">
                                    <img
                                        src="{{ asset('images/team-nayma.jpg') }}"
                                        alt="Foto Nayma Zahra Nurrizki - Tim Notulis & Tester DIHADIR"
                                        width="320" height="320"
                                        loading="lazy"
                                        class="h-full w-full rounded-[14px] object-cover object-top"
                                    >
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 font-mono text-[11px] font-bold text-slate-700 ring-1 ring-inset ring-slate-200">
                                Tester
                            </span>
                        </div>

                        {{-- Nama & Jabatan --}}
                        <div class="mt-4">
                            <h3 class="text-[17px] font-extrabold tracking-tight text-slate-900">
                                Nayma Zahra Nurrizki
                            </h3>
                            <p class="mt-1 text-xs font-bold text-slate-700">
                                Notulis &amp; Tester
                            </p>
                        </div>

                        {{-- Uraian Tugas --}}
                        <div class="mt-4 border-t border-slate-100 pt-3.5">
                            <p class="text-[12.5px] leading-relaxed text-slate-600">
                                Dokumentasi hasil evaluasi &amp; verifikasi kelancaran alur peserta.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ==================== 5. ADOPSI SISTEM UNTUK SEKOLAH LAIN ==================== --}}
    <section id="adopsi" class="py-16 sm:py-24 bg-slate-50 border-t border-slate-200/80">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="relative overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 p-8 sm:p-12 text-white shadow-xl">
                <div class="relative z-10 grid grid-cols-1 gap-8 lg:grid-cols-12 items-center">
                    <div class="lg:col-span-8 space-y-4">
                        <span class="inline-flex items-center gap-1.5 rounded-md bg-white/10 px-3 py-1 text-xs font-semibold text-slate-200">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                            </svg>
                            <span>Program Kemitraan & Transformasi Digital Antar-Sekolah</span>
                        </span>

                        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                            Sekolah Anda Berminat Menggunakan Sistem DIHADIR?
                        </h2>

                        <p class="text-xs sm:text-sm leading-relaxed text-slate-300 max-w-2xl">
                            SMKN 1 Majalengka membuka ruang kolaborasi, benchmarking, dan transfer teknologi bagi sekolah negeri maupun swasta di seluruh Indonesia yang ingin menerapkan manajemen presensi digital tanpa kertas untuk rapat komite, musyawarah dewan guru, workshop IHT, serta evaluasi kemitraan industri.
                        </p>

                        <div class="pt-2 flex flex-wrap gap-4 text-xs text-slate-300">
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Konsultasi Teknis & Panduan Instalasi</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Kustomisasi Form Sesuai Tata Kelola</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                <span>Pelatihan Panitia & Operator</span>
                            </div>
                        </div>
                    </div>

                    {{-- Right CTA contact box --}}
                    <div class="lg:col-span-4 bg-white/5 rounded-xl p-6 border border-white/10 text-center space-y-4">
                        <h3 class="text-sm font-bold text-white">Hubungi Tim Resmi SMKN 1 Majalengka</h3>
                        <p class="text-xs text-slate-300">
                            Silakan kirimkan surat permohonan kemitraan atau hubungi narahubung sekolah:
                        </p>

                        <div class="space-y-2 text-xs">
                            <a
                                href="mailto:info@smkn1majalengka.sch.id?subject=Permohonan%20Adopsi%20Sistem%20DIHADIR"
                                class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 font-bold text-slate-900 shadow-sm transition hover:bg-slate-100"
                            >
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>Kirim Surel Kemitraan</span>
                            </a>

                            <a
                                href="https://smkn1majalengka.sch.id"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-xl border border-white/20 bg-transparent px-4 py-2.5 font-semibold text-white transition hover:bg-white/10"
                            >
                                <span>Kunjungi Portal Sekolah</span>
                            </a>
                        </div>

                        <p class="text-[11px] text-slate-400">
                            Jl. Tonjong-Pinangraja No. 55, Majalengka · Telp: (0233) 282913
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.landing>
