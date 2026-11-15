@props([
    'title' => null,
    'description' => null,
    'canonical' => null,
    'ogImage' => null,
    'maxWidth' => 'max-w-7xl',
])

@php
    $pageTitle = $title ? $title . ' | DIHADIR' : 'DIHADIR — Sistem Presensi Digital Pertemuan Resmi SMKN 1 Majalengka';
    $metaDescription = $description ?? 'Platform presensi digital resmi SMKN 1 Majalengka. Pengelolaan absensi pertemuan, QR Code instan, tanda tangan digital touchscreen, dan rekapitulasi laporan resmi berstandar kedinasan.';
    $canonicalUrl = $canonical ?? url()->current();
    $metaImage = $ogImage ?? asset('images/logo-dihadir-full.webp');
@endphp

<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#334155">

    {{-- SEO Primary Meta Tags --}}
    <title>{{ $pageTitle }}</title>
    <meta name="title" content="{{ $pageTitle }}">
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="author" content="SMK Negeri 1 Majalengka">
    <meta name="google-site-verification" content="ypD_vcbzYWl6b6_kKaNMadrAWGfxl2UhW2ev2mvjY7g">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:image" content="{{ $metaImage }}">
    <meta property="og:locale" content="id_ID">
    <meta property="og:site_name" content="DIHADIR SMKN 1 Majalengka">

    {{-- Twitter / X Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">
    <meta name="twitter:image" content="{{ $metaImage }}">

    {{-- Favicon & App Icons --}}
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('images/logo-dihadir-icon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-dihadir-icon.webp') }}">

    <style>[x-cloak] { display: none !important; }</style>

    @stack('head')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 font-sans text-slate-900 antialiased selection:bg-blue-500 selection:text-white">
    {{-- ==================== NAVBAR ==================== --}}
    <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="{{ $maxWidth }} mx-auto flex h-[76px] items-center justify-between gap-4 px-4 sm:h-[84px] sm:px-6 lg:px-8">
            {{-- Brand DIHADIR --}}
            <a
                href="{{ url('/') }}"
                class="group flex items-center gap-3 rounded-xl py-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                <img
                    src="{{ asset('images/logo-dihadir-full-trimmed.webp') }}"
                    alt="DIHADIR"
                    width="1623" height="400"
                    class="h-10 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02] sm:h-11"
                >
            </a>

            {{-- Navigation Links (Desktop) --}}
            <nav class="hidden items-center gap-8 md:flex">
                <a href="#fitur" class="text-sm font-medium text-slate-600 transition-colors hover:text-blue-600">Fitur Utama</a>
                <a href="#alur" class="text-sm font-medium text-slate-600 transition-colors hover:text-blue-600">Alur Presensi</a>
                <a href="#pengembang" class="text-sm font-medium text-slate-600 transition-colors hover:text-blue-600">Pengembang</a>
                <a href="#adopsi" class="text-sm font-medium text-slate-600 transition-colors hover:text-blue-600">Adopsi Sekolah</a>
            </nav>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('absensi.login') }}"
                    class="hidden rounded-xl border border-slate-300 bg-white px-3.5 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 sm:inline-flex"
                >
                    Presensi Peserta
                </a>

                @auth
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        <span>Dashboard</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                @else
                    <a
                        href="{{ route('admin.login') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-800 px-4 py-2 text-xs font-semibold text-white shadow-sm transition hover:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-700 focus:ring-offset-2"
                    >
                        <span>Login Admin</span>
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ==================== MAIN CONTENT ==================== --}}
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="border-t border-slate-200 bg-white text-slate-600">
        <div class="{{ $maxWidth }} mx-auto px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 md:grid-cols-4">
                {{-- Col 1: Identity --}}
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <img
                            src="{{ asset('images/logo-dihadir-full-trimmed.webp') }}"
                            alt="DIHADIR"
                            class="h-9 w-auto object-contain"
                        >
                    </div>
                    <p class="max-w-md text-xs leading-relaxed text-slate-500">
                        DIHADIR (Digital Attendance Management System) adalah platform presensi digital resmi SMKN 1 Majalengka yang dirancang untuk mewujudkan pencatatan kehadiran kegiatan sekolah yang akuntabel, bebas manipulasi, dan bebas kertas.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a
                            href="https://smkn1majalengka.sch.id"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-100"
                        >
                            <img src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}" alt="Lambang SMKN 1 Majalengka" width="20" height="20" class="h-4 w-4 object-contain">
                            <span>SMK Negeri 1 Majalengka</span>
                        </a>
                        <span class="text-xs text-slate-400">NPSN: 20213853</span>
                    </div>
                </div>

                {{-- Col 2: Navigasi --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Navigasi Utama</p>
                    <ul class="mt-4 space-y-2 text-xs">
                        <li><a href="{{ url('/') }}" class="hover:text-blue-600">Beranda</a></li>
                        <li><a href="{{ route('absensi.login') }}" class="hover:text-blue-600">Presensi Kehadiran Peserta</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-blue-600">Portal Pengelola (Admin)</a></li>
                        <li><a href="{{ url('/#alur') }}" class="hover:text-blue-600">Alur Pengisian Presensi</a></li>
                        <li><a href="{{ url('/#adopsi') }}" class="hover:text-blue-600">Kemitraan & Adopsi Sistem</a></li>
                    </ul>
                </div>

                {{-- Col 3: Kebijakan & Pengembang --}}
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-900">Kebijakan & Inovasi</p>
                    <ul class="mt-4 space-y-2 text-xs">
                        <li><a href="{{ route('legal.privacy') }}" class="hover:text-blue-600">Kebijakan Privasi (PDP)</a></li>
                        <li><a href="{{ route('legal.terms') }}" class="hover:text-blue-600">Syarat & Ketentuan Layanan</a></li>
                        <li><a href="{{ url('/#pengembang') }}" class="hover:text-blue-600">Tim Pengembang SMKN 1 Majalengka</a></li>
                        <li><a href="mailto:info@smkn1majalengka.sch.id" class="hover:text-blue-600">Bantuan & Kontak Resmi</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-12 flex flex-col items-center justify-between gap-4 border-t border-slate-100 pt-6 text-[11px] text-slate-400 sm:flex-row">
                <div class="flex flex-col sm:flex-row items-center gap-3">
                    <p>&copy; {{ date('Y') }} DIHADIR · SMK Negeri 1 Majalengka. Seluruh hak cipta dilindungi.</p>
                    <span class="hidden sm:inline text-slate-300">•</span>
                    {{-- DMCA Protection Badge Resmi --}}
                    <a
                        href="//www.dmca.com/Protection/Status.aspx?ID=3b30e653-c690-4e96-9aa7-eb3040f2efbe"
                        title="DMCA.com Protection Status"
                        class="dmca-badge inline-flex items-center transition-opacity hover:opacity-80"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <img
                            src="{{ asset('images/dmca_protected_sml_120n.png') }}"
                            alt="DMCA.com Protection Status"
                            width="121"
                            height="24"
                            class="h-5 w-auto object-contain"
                        />
                    </a>
                </div>
                <p class="mt-2 sm:mt-0">Dikelola oleh Tim IT & Pengembang SMKN 1 Majalengka · Standar Waktu WIB (Asia/Jakarta)</p>
            </div>
        </div>
    </footer>
    {{-- Scroll To Top --}}
    <x-scroll-to-top />

    {{-- DMCA Protection Badge Script --}}
    <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>
</body>
</html>
