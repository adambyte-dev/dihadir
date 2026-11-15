@props([
    'title' => null,
    'maxWidth' => 'max-w-6xl',
])

@php
    $pageTitle = $title ?? config('app.name', 'DIHADIR');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1E293B">

    <title>{{ $pageTitle }} | SMKN 1 Majalengka</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('images/logo-dihadir-icon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-dihadir-icon.webp') }}">

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-slate-50 text-slate-900 antialiased">
    {{-- ==================== HEADER ==================== --}}
    <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/95 backdrop-blur-md">
        <div class="{{ $maxWidth }} mx-auto flex h-[76px] items-center justify-between gap-4 px-4 sm:h-[84px] sm:px-6 lg:px-8">
            {{-- Brand DIHADIR --}}
            <a
                href="{{ url('/') }}"
                class="group flex items-center gap-3 rounded-xl py-1 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
            >
                <img
                    src="{{ asset('images/logo-dihadir-full-trimmed.webp') }}"
                    alt="DIHADIR"
                    width="1623" height="400"
                    class="h-10 w-auto object-contain transition-transform duration-200 group-hover:scale-[1.02] sm:h-12"
                >
            </a>

            {{-- Identitas sekolah --}}
            <a
                href="https://smkn1majalengka.sch.id"
                target="_blank"
                rel="noopener noreferrer"
                class="group flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/70 px-2.5 py-1.5 transition-colors hover:border-slate-300 hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                <img
                    src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}"
                    alt="Lambang SMK Negeri 1 Majalengka"
                    width="1196" height="1127"
                    class="h-11 w-11 shrink-0 object-contain drop-shadow-sm sm:h-12 sm:w-12"
                >
                <span class="hidden text-left leading-tight sm:block">
                    <span class="block text-[11px] font-bold uppercase tracking-[0.06em] text-slate-900">SMKN 1 Majalengka</span>
                    <span class="mt-0.5 block text-[10px] text-slate-500">Sistem Presensi Resmi</span>
                </span>
            </a>
        </div>
    </header>

    {{-- ==================== KONTEN ==================== --}}
    <main class="flex flex-1 flex-col justify-center px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
        <div class="{{ $maxWidth }} mx-auto w-full">
            {{ $slot }}
        </div>
    </main>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="mt-auto border-t border-slate-200/80 bg-white">
        <div class="{{ $maxWidth }} mx-auto flex flex-col items-center justify-between gap-3 px-4 py-5 text-[11px] text-slate-500 sm:flex-row sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-2.5">
                <img
                    src="{{ asset('images/logo-dihadir-icon-trimmed.webp') }}"
                    alt=""
                    aria-hidden="true"
                    class="h-5 w-5 object-contain opacity-70"
                >
                <p>&copy; {{ date('Y') }} <span class="font-semibold text-slate-700">DIHADIR</span> · SMK Negeri 1 Majalengka</p>
                <span class="text-slate-300">•</span>
                {{-- DMCA Badge --}}
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
                        class="h-4 w-auto object-contain"
                    />
                </a>
            </div>
            <div class="flex items-center gap-2 text-slate-500">
                <span>Jl. Raya Tonjong–Pinangraja No. 55, Majalengka</span>
                <span class="text-slate-300">•</span>
                <span>WIB (Asia/Jakarta)</span>
            </div>
        </div>
    </footer>
    {{-- Scroll To Top --}}
    <x-scroll-to-top />

    {{-- DMCA Protection Badge Script --}}
    <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"></script>
</body>
</html>
