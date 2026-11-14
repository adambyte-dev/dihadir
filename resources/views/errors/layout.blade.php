<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1E293B">
    <title>@yield('title') - DIHADIR SMKN 1 Majalengka</title>

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('images/logo-dihadir-icon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-dihadir-icon.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-full flex-col bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white">
    <div class="flex flex-1 items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-lg text-center">
            {{-- Brand Mark --}}
            <div class="mb-6 inline-flex items-center justify-center gap-3">
                <img src="{{ asset('images/logo-smkn1majalengka.webp') }}" alt="Logo SMKN 1 Majalengka" class="h-10 w-10 object-contain drop-shadow-xs" width="40" height="40">
                <div class="h-6 w-px bg-slate-200"></div>
                <img src="{{ asset('images/logo-dihadir-full.webp') }}" alt="DIHADIR" class="h-8 object-contain" height="32">
            </div>

            {{-- Card Container --}}
            <div class="rounded-2xl border border-slate-200/90 bg-white p-8 shadow-xl shadow-slate-900/5 sm:p-10">
                {{-- Status Code Badge --}}
                <div class="mx-auto mb-4 inline-flex items-center justify-center rounded-xl bg-blue-50 px-3.5 py-1.5 ring-1 ring-inset ring-blue-200/70">
                    <span class="text-xs font-bold tracking-wider text-blue-700 uppercase">Status @yield('code')</span>
                </div>

                {{-- Heading --}}
                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    @yield('message')
                </h1>

                {{-- Subtitle / Description --}}
                <p class="mt-3 text-xs leading-relaxed text-slate-600 sm:text-sm">
                    @yield('description', 'Halaman atau tindakan yang Anda minta tidak dapat diproses saat ini. Silakan kembali ke beranda atau hubungi administrator.')
                </p>

                {{-- Actions --}}
                <div class="mt-8 flex flex-col items-center justify-center gap-2.5 sm:flex-row">
                    <a
                        href="{{ url('/') }}"
                        class="inline-flex min-h-[42px] w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-5 text-xs font-semibold text-white shadow-sm shadow-blue-500/20 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 sm:w-auto"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Kembali ke Beranda</span>
                    </a>
                    <button
                        type="button"
                        onclick="window.history.back()"
                        class="inline-flex min-h-[42px] w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 text-xs font-semibold text-slate-700 shadow-2xs transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-400 sm:w-auto"
                    >
                        <span>Halaman Sebelumnya</span>
                    </button>
                </div>
            </div>

            {{-- Footer Note --}}
            <p class="mt-6 text-[11px] text-slate-600">
                &copy; {{ date('Y') }} SMKN 1 Majalengka &bull; Perpustakaan Wacana Teknologi
            </p>
        </div>
    </div>
</body>
</html>
