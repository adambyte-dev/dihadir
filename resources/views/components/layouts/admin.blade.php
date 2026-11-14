@props([
    'title' => null,
    'heading' => null,
])

@php
    $pageTitle = $title ?? 'Panel Admin';
    $pageHeading = $heading ?? $title ?? 'Dashboard';

    $navSections = [
        'Utama' => [
            [
                'label' => 'Dashboard',
                'route' => 'admin.dashboard',
                'active' => 'admin.dashboard',
                'path' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z',
            ],
        ],
        'Manajemen Kegiatan' => [
            [
                'label' => 'Data Pertemuan',
                'route' => 'admin.pertemuan.index',
                'active' => 'admin.pertemuan.*',
                'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            ],
            [
                'label' => 'Data Peserta',
                'route' => 'admin.peserta.index',
                'active' => 'admin.peserta.*',
                'path' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
            ],
        ],
        'Data & Dokumen' => [
            [
                'label' => 'Rekap Kehadiran',
                'route' => 'admin.rekap.index',
                'active' => 'admin.rekap.*',
                'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
            ],
            [
                'label' => 'Laporan Resmi',
                'route' => 'admin.laporan.index',
                'active' => 'admin.laporan.*',
                'path' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            ],
        ],
        'Sistem' => [
            [
                'label' => 'Pengaturan',
                'route' => 'admin.pengaturan.index',
                'active' => 'admin.pengaturan.*',
                'path' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z',
            ],
        ],
    ];

    if (auth()->user()?->isSuperAdmin()) {
        $navSections['Sistem'][] = [
            'label' => 'Kelola Admin',
            'route' => 'admin.users.index',
            'active' => 'admin.users.*',
            'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
        ];
        $navSections['Sistem'][] = [
            'label' => 'Audit Log',
            'route' => 'admin.audit.index',
            'active' => 'admin.audit.*',
            'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
        ];
    }

    // Menu bawah untuk mobile, maksimal 5 item agar tidak sempit.
    $mobileTabs = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'active' => 'admin.dashboard', 'path' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
        ['label' => 'Pertemuan', 'route' => 'admin.pertemuan.index', 'active' => 'admin.pertemuan.*', 'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
        ['label' => 'Peserta', 'route' => 'admin.peserta.index', 'active' => 'admin.peserta.*', 'path' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['label' => 'Rekap', 'route' => 'admin.rekap.index', 'active' => 'admin.rekap.*', 'path' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
        ['label' => 'Laporan', 'route' => 'admin.laporan.index', 'active' => 'admin.laporan.*', 'path' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];

    $currentUser = auth()->user();
    $initials = collect(explode(' ', trim($currentUser->name ?? 'A')))
        ->filter()
        ->take(2)
        ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
        ->implode('');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0F172A">

    <title>{{ $pageTitle }} | DIHADIR SMKN 1 Majalengka</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/webp" sizes="32x32" href="{{ asset('images/logo-dihadir-icon.webp') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-dihadir-icon.webp') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    class="h-full overflow-x-hidden bg-slate-100 font-sans text-slate-900 antialiased" 
    x-data="{ 
        menuSheet: false,
        sidebarCollapsed: localStorage.getItem('dihadir_sidebar_collapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('dihadir_sidebar_collapsed', this.sidebarCollapsed);
        }
    }"
>
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:left-3 focus:top-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2.5 focus:text-sm focus:font-semibold focus:text-slate-900 focus:shadow-lg">
        Lewati ke konten utama
    </a>

    <div class="flex min-h-full">
        {{-- ==================== SIDEBAR (desktop) ==================== --}}
        <aside 
            class="sticky top-0 hidden h-screen shrink-0 flex-col border-r border-slate-950/50 bg-gradient-to-b from-[#0F172A] via-[#152238] to-[#0B1220] text-slate-100 lg:flex transition-all duration-300 ease-in-out"
            :class="sidebarCollapsed ? 'w-[78px]' : 'w-[272px]'"
        >
            {{-- Identitas aplikasi & sekolah --}}
            <div class="relative shrink-0 pb-4 pt-5" :class="sidebarCollapsed ? 'px-3 text-center' : 'px-5'">
                <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-blue-400/40 to-transparent"></div>

                <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <a href="{{ route('admin.dashboard') }}" class="group flex items-center gap-3 rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white p-1 shadow-lg shadow-slate-950/40 ring-1 ring-white/25">
                            <img
                                src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}"
                                alt="Lambang SMK Negeri 1 Majalengka"
                                width="1196" height="1127"
                                class="h-full w-full object-contain"
                            >
                        </span>
                        <span class="min-w-0 leading-tight transition-opacity duration-200" x-show="!sidebarCollapsed" x-cloak>
                            <span class="block text-[15px] font-extrabold tracking-tight text-white">DIHADIR</span>
                            <span class="mt-0.5 block truncate text-[11px] font-semibold text-slate-400">SMKN 1 Majalengka</span>
                        </span>
                    </a>

                    {{-- Collapse Toggle Button di Header Sidebar --}}
                    <button 
                        type="button" 
                        @click="toggleSidebar()"
                        x-show="!sidebarCollapsed"
                        class="hidden lg:flex h-7 w-7 items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white transition"
                        title="Perkecil Sidebar"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>
                </div>

                {{-- Status aktif --}}
                <div 
                    class="mt-3.5 flex items-center rounded-lg border border-white/[0.08] bg-white/[0.04] transition-all"
                    :class="sidebarCollapsed ? 'justify-center p-2' : 'gap-2 px-2.5 py-1.5'"
                    :title="sidebarCollapsed ? 'Sistem Presensi Aktif' : ''"
                >
                    <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-400" aria-hidden="true"></span>
                    <span class="truncate text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-300" x-show="!sidebarCollapsed" x-cloak>Sistem Presensi Aktif</span>
                </div>
            </div>

            {{-- Navigasi --}}
            <nav class="flex-1 overflow-y-auto px-2.5 pb-3" aria-label="Navigasi utama">
                @foreach ($navSections as $section => $items)
                    <div class="mb-4">
                        <p 
                            class="mb-1.5 px-3 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-500 transition-opacity" 
                            x-show="!sidebarCollapsed" 
                            x-cloak
                        >
                            {{ $section }}
                        </p>
                        <div x-show="sidebarCollapsed" class="my-2 border-t border-white/[0.06] mx-1" x-cloak></div>

                        <ul class="space-y-1">
                            @foreach ($items as $item)
                                @php $isActive = request()->routeIs($item['active']); @endphp
                                <li>
                                    <a
                                        href="{{ route($item['route']) }}"
                                        @if ($isActive) aria-current="page" @endif
                                        :title="sidebarCollapsed ? '{{ $item['label'] }}' : ''"
                                        class="group flex items-center rounded-lg py-2.5 text-[13px] transition-colors duration-150 {{ $isActive ? 'bg-blue-600 font-semibold text-white shadow-sm shadow-blue-950/50' : 'font-medium text-slate-300 hover:bg-white/[0.07] hover:text-white' }}"
                                        :class="sidebarCollapsed ? 'justify-center px-0' : 'gap-3 px-3'"
                                    >
                                        <svg class="h-[18px] w-[18px] shrink-0 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-blue-300' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $item['path'] }}"/>
                                        </svg>
                                        <span class="truncate transition-opacity" x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="mt-1 border-t border-white/[0.07] pt-3">
                    <a
                        href="{{ route('absensi.login') }}"
                        target="_blank"
                        rel="noopener"
                        :title="sidebarCollapsed ? 'Form Login Absensi' : ''"
                        class="flex items-center rounded-lg py-2.5 text-[13px] font-medium text-slate-400 transition-colors hover:bg-white/[0.07] hover:text-white"
                        :class="sidebarCollapsed ? 'justify-center px-0' : 'gap-3 px-3'"
                    >
                        <svg class="h-[18px] w-[18px] shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        <span class="truncate" x-show="!sidebarCollapsed" x-cloak>Form Login Absensi</span>
                    </a>
                </div>
            </nav>

            {{-- Akun & keluar --}}
            <div class="shrink-0 space-y-2 border-t border-white/[0.07] p-2.5">
                <a 
                    href="{{ route('admin.pengaturan.index') }}" 
                    class="flex items-center rounded-lg bg-white/[0.05] py-2 ring-1 ring-white/[0.08] transition-colors hover:bg-white/[0.09]"
                    :class="sidebarCollapsed ? 'justify-center px-0' : 'gap-2.5 px-3'"
                    :title="sidebarCollapsed ? '{{ $currentUser->name ?? 'Administrator' }}' : ''"
                >
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-800 ring-1 ring-white/20 shadow-sm">
                        @if ($currentUser)
                            <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="text-[11px] font-bold text-white">{{ $initials ?: 'A' }}</span>
                        @endif
                    </span>
                    <span class="min-w-0 flex-1 leading-tight" x-show="!sidebarCollapsed" x-cloak>
                        <span class="block truncate text-xs font-semibold text-white">{{ $currentUser->name ?? 'Administrator' }}</span>
                        <span class="mt-0.5 block truncate text-[10px] text-slate-400">{{ $currentUser->isSuperAdmin() ? 'Super Admin' : 'Admin Pengelola' }}</span>
                    </span>
                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true" x-show="!sidebarCollapsed" x-cloak>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        :title="sidebarCollapsed ? 'Keluar Sistem' : ''"
                        class="flex min-h-[40px] w-full items-center justify-center rounded-lg border border-rose-500/25 text-xs font-semibold text-rose-300 transition-colors hover:border-rose-500/50 hover:bg-rose-600 hover:text-white"
                        :class="sidebarCollapsed ? 'px-0' : 'gap-2 px-3'"
                    >
                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" x-cloak>Keluar</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- ==================== KONTEN ==================== --}}
        <div class="flex min-w-0 flex-1 flex-col">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 border-b border-slate-200/90 bg-white/90 backdrop-blur-md">
                <div class="flex h-16 w-full items-center justify-between gap-3 px-4 sm:px-6 lg:px-8">
                    <div class="flex min-w-0 items-center gap-3">
                        {{-- Toggle Button Desktop di Topbar (terlihat terutama saat sidebar collapsed) --}}
                        <button 
                            type="button" 
                            @click="toggleSidebar()"
                            class="hidden lg:flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition shadow-2xs"
                            title="Toggle Sidebar"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </button>

                        {{-- Logo mobile --}}
                        <a href="{{ route('admin.dashboard') }}" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-slate-900 p-1 shadow-sm lg:hidden">
                            <img
                                src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}"
                                alt="SMKN 1 Majalengka"
                                width="1196" height="1127"
                                class="h-full w-full object-contain"
                            >
                        </a>

                        <div class="min-w-0">
                            <p class="hidden text-[10px] font-bold uppercase tracking-[0.13em] text-slate-500 sm:block">Panel Pengelola</p>
                            <h1 class="truncate text-[15px] font-bold tracking-tight text-slate-900 sm:text-base">{{ $pageHeading }}</h1>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
                        <div class="hidden items-center gap-2 rounded-lg border border-slate-200 bg-slate-50/80 px-3 py-1.5 md:flex">
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-[11px] font-semibold text-slate-700">{{ now()->translatedFormat('d M Y') }}</span>
                            <span class="text-slate-300" aria-hidden="true">|</span>
                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">WIB</span>
                        </div>

                        <a
                            href="{{ url('/') }}"
                            target="_blank"
                            rel="noopener"
                            class="hidden h-11 w-11 items-center justify-center rounded-lg text-slate-500 transition-colors hover:bg-blue-50 hover:text-blue-600 sm:flex"
                            title="Buka halaman depan publik"
                        >
                            <span class="sr-only">Buka halaman depan publik</span>
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>

                        <span class="hidden h-8 w-px bg-slate-200 sm:block" aria-hidden="true"></span>

                        {{-- Menu mobile (sheet) --}}
                        <button
                            type="button"
                            @click="menuSheet = true"
                            class="flex h-11 w-11 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 lg:hidden"
                        >
                            <span class="sr-only">Buka menu lainnya</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <a href="{{ route('admin.pengaturan.index') }}" class="hidden items-center gap-2 rounded-lg px-1 py-1 transition-colors hover:bg-slate-100 sm:flex">
                            <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                @if ($currentUser)
                                    <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                                @else
                                    <span class="text-[11px] font-bold text-slate-800">{{ $initials ?: 'A' }}</span>
                                @endif
                            </span>
                        </a>
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if (session('success') || session('error') || session('warning') || session('info'))
                <div class="w-full px-4 pt-4 sm:px-6 lg:px-8">
                    @if (session('success'))
                        <x-alert type="success" :dismissible="true" class="mb-3">{{ session('success') }}</x-alert>
                    @endif
                    @if (session('error'))
                        <x-alert type="error" :dismissible="true" class="mb-3">{{ session('error') }}</x-alert>
                    @endif
                    @if (session('warning'))
                        <x-alert type="warning" :dismissible="true" class="mb-3">{{ session('warning') }}</x-alert>
                    @endif
                    @if (session('info'))
                        <x-alert type="info" :dismissible="true" class="mb-3">{{ session('info') }}</x-alert>
                    @endif
                </div>
            @endif

            {{-- Halaman --}}
            <main id="main-content" class="w-full flex-1 px-4 pb-24 pt-5 sm:px-6 sm:pt-6 lg:px-8 lg:pb-8 lg:pt-8">
                <div class="w-full">
                    {{ $slot }}
                </div>
            </main>

            <footer class="w-full border-t border-slate-200/80 px-4 py-4 pb-24 sm:px-6 lg:px-8 lg:pb-4">
                <div class="flex w-full flex-col items-center justify-between gap-2 text-[11px] text-slate-500 sm:flex-row">
                    <p>&copy; {{ date('Y') }} <span class="font-semibold text-slate-700">DIHADIR</span> · SMK Negeri 1 Majalengka</p>
                    <p class="text-slate-500">Digital Attendance Management System</p>
                </div>
            </footer>
        </div>
    </div>

    {{-- ==================== MENU BAWAH (mobile) ==================== --}}
    <nav
        class="fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 backdrop-blur-md lg:hidden"
        style="padding-bottom: env(safe-area-inset-bottom);"
        aria-label="Navigasi bawah"
    >
        <ul class="grid grid-cols-5">
            @foreach ($mobileTabs as $tab)
                @php $tabActive = request()->routeIs($tab['active']); @endphp
                <li>
                    <a
                        href="{{ route($tab['route']) }}"
                        @if ($tabActive) aria-current="page" @endif
                        class="flex min-h-[58px] flex-col items-center justify-center gap-1 px-1 pt-2 pb-1.5 transition-colors {{ $tabActive ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}"
                    >
                        <span class="relative flex h-7 w-11 items-center justify-center rounded-full transition-colors {{ $tabActive ? 'bg-blue-50' : '' }}">
                            <svg class="h-[19px] w-[19px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="{{ $tabActive ? '2.2' : '1.9' }}" d="{{ $tab['path'] }}"/>
                            </svg>
                        </span>
                        <span class="text-[10px] {{ $tabActive ? 'font-bold' : 'font-medium' }}">{{ $tab['label'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>

    {{-- Sheet menu lainnya (mobile) --}}
    <div
        x-show="menuSheet"
        x-cloak
        class="fixed inset-0 z-50 lg:hidden"
        role="dialog"
        aria-modal="true"
        aria-label="Menu lainnya"
    >
        <div x-show="menuSheet" x-transition.opacity.duration.200ms @click="menuSheet = false" class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"></div>

        <div
            x-show="menuSheet"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto rounded-t-2xl bg-white shadow-2xl"
            style="padding-bottom: env(safe-area-inset-bottom);"
        >
            <div class="sticky top-0 flex items-center justify-between gap-3 border-b border-slate-100 bg-white px-4 py-3">
                <div class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-900 p-1">
                        <img src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}" alt="" aria-hidden="true" class="h-full w-full object-contain">
                    </span>
                    <div class="leading-tight">
                        <p class="text-[13px] font-bold text-slate-900">Menu Navigasi</p>
                        <p class="text-[10.5px] text-slate-500">SMKN 1 Majalengka</p>
                    </div>
                </div>
                <button type="button" @click="menuSheet = false" class="flex h-11 w-11 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100">
                    <span class="sr-only">Tutup menu</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="px-3 py-3">
                @foreach ($navSections as $section => $items)
                    <p class="mb-1 px-2 text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">{{ $section }}</p>
                    <ul class="mb-3 space-y-0.5">
                        @foreach ($items as $item)
                            @php $isActive = request()->routeIs($item['active']); @endphp
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="flex min-h-[46px] items-center gap-3 rounded-lg px-2.5 text-[13px] {{ $isActive ? 'bg-blue-50 font-semibold text-blue-700' : 'font-medium text-slate-700 hover:bg-slate-50' }}"
                                >
                                    <svg class="h-[17px] w-[17px] shrink-0 {{ $isActive ? 'text-blue-600' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $item['path'] }}"/>
                                    </svg>
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach

                <a href="{{ route('absensi.login') }}" target="_blank" rel="noopener" class="mb-2 flex min-h-[46px] items-center gap-3 rounded-lg px-2.5 text-[13px] font-medium text-slate-700 hover:bg-slate-50">
                    <svg class="h-[17px] w-[17px] shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Form Login Absensi</span>
                </a>

                <div class="border-t border-slate-100 pt-3">
                    <div class="mb-2 flex items-center gap-2.5 rounded-lg bg-slate-50 px-2.5 py-2">
                        <span class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full bg-slate-200 ring-1 ring-slate-300">
                            @if ($currentUser)
                                <img src="{{ $currentUser->avatar_url }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover">
                            @else
                                <span class="text-[11px] font-bold text-slate-800">{{ $initials ?: 'A' }}</span>
                            @endif
                        </span>
                        <div class="min-w-0 leading-tight">
                            <p class="truncate text-xs font-semibold text-slate-900">{{ $currentUser->name ?? 'Administrator' }}</p>
                            <p class="truncate text-[10px] text-slate-500">{{ $currentUser->isSuperAdmin() ? 'Super Admin' : 'Admin Pengelola' }}</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="flex min-h-[46px] w-full items-center justify-center gap-2 rounded-lg border border-rose-200 bg-rose-50 text-xs font-bold text-rose-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Keluar Sistem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Dialog Konfirmasi Khusus (Menggantikan confirm bawaan browser) --}}
    <x-confirm-dialog />
</body>
</html>
