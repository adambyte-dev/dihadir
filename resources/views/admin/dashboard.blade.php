<x-layouts.admin title="Dashboard Ringkasan" heading="Dashboard Pengelola">
    @php
        $user = auth()->user();

        $statCards = [
            [
                'label' => 'Total Pertemuan',
                'value' => $stats['total_pertemuan'],
                'caption' => 'Kegiatan terdaftar',
                'tone' => 'bg-blue-50 text-blue-600 ring-blue-100',
                'valueClass' => 'text-slate-900',
                'path' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
                'href' => route('admin.pertemuan.index'),
            ],
            [
                'label' => 'Total Peserta',
                'value' => $stats['total_peserta'],
                'caption' => 'Peserta aktif terdaftar',
                'tone' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                'valueClass' => 'text-slate-900',
                'path' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                'href' => route('admin.peserta.index'),
            ],
            [
                'label' => 'Rata-rata Kehadiran',
                'value' => $attendanceRate === null ? null : number_format($attendanceRate, 1) . '%',
                'caption' => $ratedMeetings->isNotEmpty() ? 'Dari ' . number_format($ratedMeetings->count()) . ' pertemuan berpeserta' : 'Belum ada data kehadiran',
                'tone' => 'bg-indigo-50 text-indigo-600 ring-indigo-100',
                'valueClass' => 'text-slate-900',
                'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                'href' => route('admin.rekap.index'),
            ],
            [
                'label' => 'Laporan Tersedia',
                'value' => $reportsAvailable,
                'caption' => 'Pertemuan dengan presensi valid',
                'tone' => 'bg-amber-50 text-amber-600 ring-amber-100',
                'valueClass' => 'text-slate-900',
                'path' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'href' => route('admin.laporan.index'),
            ],
        ];

        // ---- Grafik tren kehadiran (SVG murni, tanpa pustaka luar) ----
        $chartW = 640;
        $chartH = 200;
        $padL = 44;
        $padR = 16;
        $padT = 16;
        $padB = 34;
        $plotW = $chartW - $padL - $padR;
        $plotH = $chartH - $padT - $padB;
        $pointCount = max(1, $monthlyTrend->count());
        $stepX = $pointCount > 1 ? $plotW / ($pointCount - 1) : 0;

        $points = [];
        foreach ($monthlyTrend->values() as $i => $point) {
            $x = $padL + ($i * $stepX);
            if ($point['rate'] === null) {
                $points[] = ['x' => round($x, 2), 'y' => null, 'data' => $point];
            } else {
                $y = $padT + $plotH - (($point['rate'] / 100) * $plotH);
                $points[] = ['x' => round($x, 2), 'y' => round($y, 2), 'data' => $point];
            }
        }

        $withData = array_values(array_filter($points, fn ($p) => $p['y'] !== null));

        // Pecah garis menjadi beberapa segmen agar bulan tanpa data tidak disambung palsu.
        $segments = [];
        $current = [];
        foreach ($points as $p) {
            if ($p['y'] === null) {
                if (count($current) > 0) {
                    $segments[] = $current;
                    $current = [];
                }
            } else {
                $current[] = $p;
            }
        }
        if (count($current) > 0) {
            $segments[] = $current;
        }
        $segments = array_values(array_filter($segments, fn ($s) => count($s) > 1));

        $areaPaths = [];
        $baseline = $padT + $plotH;
        foreach ($segments as $seg) {
            $first = $seg[0];
            $last = $seg[count($seg) - 1];
            $areaPaths[] = 'M ' . $first['x'] . ' ' . $baseline . ' L '
                . implode(' L ', array_map(fn ($p) => $p['x'] . ' ' . $p['y'], $seg))
                . ' L ' . $last['x'] . ' ' . $baseline . ' Z';
        }

        $gridLines = [0, 25, 50, 75, 100];

        // ---- Donut ringkasan ----
        $donutR = 52;
        $donutC = 2 * M_PI * $donutR;
        $donutSegments = [];
        $accumulated = 0;
        foreach ([
            ['key' => 'valid', 'label' => 'Hadir', 'color' => '#16A34A'],
            ['key' => 'flagged', 'label' => 'Perlu Periksa', 'color' => '#F59E0B'],
            ['key' => 'cancelled', 'label' => 'Dibatalkan', 'color' => '#DC2626'],
        ] as $seg) {
            $count = $breakdown[$seg['key']];
            $portion = $totalSubmissions > 0 ? $count / $totalSubmissions : 0;
            $length = $donutC * $portion;
            $donutSegments[] = [
                'label' => $seg['label'],
                'color' => $seg['color'],
                'count' => $count,
                'dash' => round($length, 2),
                'gap' => round($donutC - $length, 2),
                'offset' => round(-$accumulated, 2),
                'percent' => $totalSubmissions > 0 ? round($portion * 100, 1) : 0,
            ];
            $accumulated += $length;
        }
    @endphp

    <div class="space-y-5 sm:space-y-6">
        {{-- ============ SAMBUTAN ============ --}}
        <section class="relative overflow-hidden rounded-xl border border-slate-200 bg-white p-5 sm:p-6 shadow-2xs">
            <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">
                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                        Sistem Presensi Aktif
                    </span>
                    <h2 class="text-[18px] font-extrabold leading-tight tracking-tight text-slate-900 sm:text-xl">
                        Selamat bertugas, {{ \Illuminate\Support\Str::before($user->name ?? 'Administrator', ' ') }}
                    </h2>
                    <p class="mt-1 text-xs text-slate-600">
                        Ringkasan operasional dan rekapitulasi data kehadiran pertemuan SMKN 1 Majalengka.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                    <a
                        href="{{ route('admin.pertemuan.create') }}"
                        class="inline-flex min-h-[44px] items-center gap-2 rounded-lg bg-blue-600 px-4 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Pertemuan</span>
                    </a>
                    <a
                        href="{{ route('admin.rekap.index') }}"
                        class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-xs font-semibold text-slate-700 shadow-2xs transition hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                    >
                        <span>Rekap Kehadiran</span>
                    </a>
                </div>
            </div>
        </section>

        {{-- ============ STATISTIK ============ --}}
        <section aria-label="Ringkasan statistik">
            <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                @foreach ($statCards as $card)
                    <a
                        href="{{ $card['href'] }}"
                        class="group flex flex-col rounded-xl border border-slate-200/90 bg-white p-3.5 shadow-sm transition-all duration-150 hover:border-slate-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2 sm:p-4"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ring-1 ring-inset {{ $card['tone'] }} sm:h-10 sm:w-10">
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $card['path'] }}"/>
                                </svg>
                            </span>
                            <svg class="mt-1 h-3.5 w-3.5 shrink-0 text-slate-300 transition-colors group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>

                        <p class="tabular mt-2.5 text-[22px] font-extrabold leading-none tracking-tight sm:text-[26px] {{ $card['valueClass'] }}">
                            @if ($card['value'] === null)
                                <span class="text-slate-300">·</span>
                            @else
                                {{ is_string($card['value']) ? $card['value'] : number_format($card['value']) }}
                            @endif
                        </p>
                        <p class="mt-1 text-[11.5px] font-bold leading-snug text-slate-800">{{ $card['label'] }}</p>
                        <p class="mt-0.5 text-[10.5px] leading-snug text-slate-500">{{ $card['caption'] }}</p>
                    </a>
                @endforeach
            </div>
        </section>

        {{-- ============ GRAFIK + PERTEMUAN TERBARU ============ --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.35fr)_minmax(0,1fr)] xl:gap-6">
            {{-- Grafik kehadiran --}}
            <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-[13.5px] font-bold leading-snug text-slate-900">Grafik Kehadiran</h3>
                            <p class="mt-0.5 text-[11px] text-slate-500">Persentase kehadiran per bulan pelaksanaan.</p>
                        </div>
                    </div>
                    <span class="hidden shrink-0 rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-[10.5px] font-bold text-slate-600 sm:block">
                        6 Bulan Terakhir
                    </span>
                </div>

                @if ($hasTrendData)
                    <div class="p-4 sm:p-5">
                        <svg
                            viewBox="0 0 {{ $chartW }} {{ $chartH }}"
                            class="h-auto w-full"
                            role="img"
                            aria-label="Grafik garis persentase kehadiran enam bulan terakhir"
                        >
                            {{-- Grid horizontal --}}
                            @foreach ($gridLines as $value)
                                @php
                                    $gy = $padT + $plotH - (($value / 100) * $plotH);
                                @endphp
                                <line x1="{{ $padL }}" y1="{{ $gy }}" x2="{{ $chartW - $padR }}" y2="{{ $gy }}" stroke="#E2E8F0" stroke-width="1" />
                                <text x="{{ $padL - 8 }}" y="{{ $gy + 3.5 }}" text-anchor="end" font-size="10" fill="#94A3B8" font-family="Inter, sans-serif">{{ $value }}%</text>
                            @endforeach

                            {{-- Area --}}
                            @foreach ($areaPaths as $areaPath)
                                <path d="{{ $areaPath }}" fill="#3B82F6" fill-opacity="0.08" />
                            @endforeach

                            {{-- Garis per segmen bersambung --}}
                            @foreach ($segments as $seg)
                                <polyline
                                    points="{{ implode(' ', array_map(fn ($p) => $p['x'] . ',' . $p['y'], $seg)) }}"
                                    fill="none"
                                    stroke="#3B82F6"
                                    stroke-width="2.5"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                />
                            @endforeach

                            {{-- Titik data --}}
                            @foreach ($withData as $p)
                                <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#FFFFFF" stroke="#3B82F6" stroke-width="2.5" />
                            @endforeach

                            {{-- Label bulan --}}
                            @foreach ($points as $p)
                                <text
                                    x="{{ $p['x'] }}"
                                    y="{{ $chartH - 12 }}"
                                    text-anchor="middle"
                                    font-size="10.5"
                                    fill="{{ $p['y'] === null ? '#CBD5E1' : '#64748B' }}"
                                    font-family="Inter, sans-serif"
                                    font-weight="600"
                                >{{ $p['data']['label'] }}</text>
                            @endforeach
                        </svg>

                        <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3">
                            <p class="text-[11px] text-slate-500">
                                Total kehadiran sah: <span class="tabular font-bold text-slate-800">{{ number_format($stats['sudah_hadir']) }}</span>
                            </p>
                            <p class="text-[11px] text-slate-500">
                                Peserta aktif: <span class="tabular font-bold text-slate-800">{{ number_format($stats['total_peserta']) }}</span>
                            </p>
                        </div>
                    </div>
                @else
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/>
                            </svg>
                        </div>
                        <h4 class="text-[13.5px] font-bold text-slate-900">Grafik Belum Tersedia</h4>
                        <p class="mx-auto mt-1 max-w-sm text-[11.5px] leading-relaxed text-slate-500">
                            Grafik dihitung dari kehadiran nyata. Belum ada pertemuan dengan peserta terdaftar pada enam bulan terakhir.
                        </p>
                        <a href="{{ route('admin.pertemuan.create') }}" class="mt-4 inline-flex min-h-[44px] items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 text-[11.5px] font-bold text-white shadow-sm transition-colors hover:bg-blue-700">
                            Buat Pertemuan
                        </a>
                    </div>
                @endif
            </section>

            {{-- Pertemuan terbaru --}}
            <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </span>
                        <h3 class="text-[13.5px] font-bold leading-snug text-slate-900">Pertemuan Terbaru</h3>
                    </div>
                    <a href="{{ route('admin.pertemuan.index') }}" class="inline-flex min-h-[44px] shrink-0 items-center gap-1 rounded-md px-1.5 text-[11px] font-bold text-blue-600 transition-colors hover:bg-blue-50">
                        <span>Lihat Semua</span>
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>

                @if ($recentMeetings->isNotEmpty())
                    <ul class="divide-y divide-slate-100">
                        @foreach ($recentMeetings as $meeting)
                            @php
                                $pct = $meeting->participants_count > 0
                                    ? round(($meeting->valid_attendances_count / $meeting->participants_count) * 100, 1)
                                    : null;
                                $pctTone = $pct === null
                                    ? 'bg-slate-100 text-slate-600 ring-slate-200'
                                    : ($pct >= 75
                                        ? 'bg-emerald-50 text-emerald-700 ring-emerald-200'
                                        : ($pct >= 40
                                            ? 'bg-amber-50 text-amber-700 ring-amber-200'
                                            : 'bg-rose-50 text-rose-700 ring-rose-200'));
                            @endphp
                            <li>
                                <a href="{{ route('admin.pertemuan.show', $meeting) }}" class="flex items-center gap-3 px-4 py-3 transition-colors hover:bg-slate-50/80 sm:px-5">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                        <svg class="h-[17px] w-[17px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-[12.5px] font-bold leading-snug text-slate-900">{{ $meeting->title }}</p>
                                        <p class="mt-0.5 truncate text-[11px] text-slate-500">
                                            {{ $meeting->meeting_date?->translatedFormat('d M Y') ?? 'Tanggal belum diisi' }}
                                            <span class="text-slate-300" aria-hidden="true">•</span>
                                            {{ number_format($meeting->valid_attendances_count) }}/{{ number_format($meeting->participants_count) }} hadir
                                        </p>
                                    </div>

                                    <span class="tabular inline-flex shrink-0 items-center rounded-md px-2 py-1 text-[11px] font-bold ring-1 ring-inset {{ $pctTone }}">
                                        {{ $pct === null ? 'N/A' : number_format($pct, 1) . '%' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="px-5 py-12 text-center">
                        <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-lg bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-[13.5px] font-bold text-slate-900">Belum ada kegiatan pertemuan yang terdaftar</h4>
                        <p class="mx-auto mt-1 max-w-xs text-[11.5px] leading-relaxed text-slate-500">
                            Buat pertemuan perdana untuk menerbitkan formulir absensi digital bagi peserta.
                        </p>
                    </div>
                @endif
            </section>
        </div>

        {{-- ============ RINGKASAN + INFORMASI ============ --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,1.2fr)] xl:gap-6">
            {{-- Donut ringkasan --}}
            <section class="flex flex-col justify-between overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3.5 sm:px-5">
                        <div class="flex items-center gap-2.5">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                                </svg>
                            </span>
                            <div>
                                <h3 class="text-[13.5px] font-bold leading-snug text-slate-900">Ringkasan Kehadiran</h3>
                                <p class="text-[11px] text-slate-500">Distribusi status verifikasi presensi.</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.rekap.index') }}" class="text-[11px] font-semibold text-blue-600 hover:text-blue-700">Detail Rekap &rarr;</a>
                    </div>

                    @if ($totalSubmissions > 0)
                        <div class="p-4 sm:p-5">
                            <div class="flex flex-col items-center gap-5 sm:flex-row sm:items-center">
                                <div class="relative shrink-0">
                                    <svg viewBox="0 0 140 140" class="h-[128px] w-[128px] -rotate-90" role="img" aria-label="Diagram donat komposisi status kehadiran">
                                        <circle cx="70" cy="70" r="{{ $donutR }}" fill="none" stroke="#F1F5F9" stroke-width="16"/>
                                        @foreach ($donutSegments as $seg)
                                            @if ($seg['count'] > 0)
                                                <circle
                                                    cx="70" cy="70" r="{{ $donutR }}"
                                                    fill="none"
                                                    stroke="{{ $seg['color'] }}"
                                                    stroke-width="16"
                                                    stroke-dasharray="{{ $seg['dash'] }} {{ $seg['gap'] }}"
                                                    stroke-dashoffset="{{ $seg['offset'] }}"
                                                    stroke-linecap="butt"
                                                />
                                            @endif
                                        @endforeach
                                    </svg>
                                    <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                                        <span class="tabular text-[20px] font-extrabold leading-none text-slate-900">
                                            {{ number_format(($breakdown['valid'] / $totalSubmissions) * 100, 1) }}%
                                        </span>
                                        <span class="mt-0.5 text-[10px] font-bold uppercase tracking-[0.06em] text-slate-500">Hadir</span>
                                    </div>
                                </div>

                                <dl class="w-full min-w-0 space-y-2.5">
                                    @foreach ($donutSegments as $seg)
                                        <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                            <dt class="flex min-w-0 items-center gap-2">
                                                <span class="h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $seg['color'] }};" aria-hidden="true"></span>
                                                <span class="truncate text-[12px] font-medium text-slate-700">{{ $seg['label'] }}</span>
                                            </dt>
                                            <dd class="tabular shrink-0 text-[12.5px] font-bold text-slate-900">{{ number_format($seg['count']) }}</dd>
                                        </div>
                                    @endforeach
                                    <div class="flex items-center justify-between gap-3 pt-1 border-t border-slate-200">
                                        <dt class="text-[11px] font-bold uppercase tracking-[0.06em] text-slate-500">Total Respons</dt>
                                        <dd class="tabular text-[13px] font-extrabold text-slate-900">{{ number_format($totalSubmissions) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            {{-- Mini metrik bar pengisi ruang bawah agar tidak kopong --}}
                            <div class="mt-5 grid grid-cols-3 gap-2 border-t border-slate-100 pt-4 text-center">
                                <div class="rounded-lg bg-emerald-50/60 p-2.5 border border-emerald-100/60">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Valid</p>
                                    <p class="tabular text-sm font-extrabold text-emerald-900 mt-0.5">{{ number_format($breakdown['valid']) }}</p>
                                    <p class="text-[10px] text-emerald-600 mt-0.5">{{ $totalSubmissions > 0 ? round(($breakdown['valid'] / $totalSubmissions) * 100) : 0 }}%</p>
                                </div>
                                <div class="rounded-lg bg-amber-50/60 p-2.5 border border-amber-100/60">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Ditandai</p>
                                    <p class="tabular text-sm font-extrabold text-amber-900 mt-0.5">{{ number_format($breakdown['flagged']) }}</p>
                                    <p class="text-[10px] text-amber-600 mt-0.5">{{ $totalSubmissions > 0 ? round(($breakdown['flagged'] / $totalSubmissions) * 100) : 0 }}%</p>
                                </div>
                                <div class="rounded-lg bg-rose-50/60 p-2.5 border border-rose-100/60">
                                    <p class="text-[10px] font-bold uppercase tracking-wider text-rose-700">Batal</p>
                                    <p class="tabular text-sm font-extrabold text-rose-900 mt-0.5">{{ number_format($breakdown['cancelled']) }}</p>
                                    <p class="text-[10px] text-rose-600 mt-0.5">{{ $totalSubmissions > 0 ? round(($breakdown['cancelled'] / $totalSubmissions) * 100) : 0 }}%</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="px-5 py-12 text-center">
                            <div class="mx-auto mb-3 flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                                </svg>
                            </div>
                            <h4 class="text-[13.5px] font-bold text-slate-900">Belum Ada Respons Kehadiran</h4>
                            <p class="mx-auto mt-1 max-w-xs text-[11.5px] leading-relaxed text-slate-500">
                                Ringkasan akan tampil setelah peserta mengirimkan formulir absensi pada pertemuan yang terbit.
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Footer card --}}
                <div class="border-t border-slate-100 bg-slate-50/70 px-4 py-2.5 text-xs text-slate-500 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 text-[11px]">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                        Data real-time terenkripsi
                    </span>
                    <a href="{{ route('admin.laporan.index') }}" class="text-[11px] font-semibold text-slate-700 hover:text-blue-600">Ekspor Laporan</a>
                </div>
            </section>

            {{-- Informasi --}}
            <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <div class="flex items-center gap-2.5 border-b border-slate-100 px-4 py-3.5 sm:px-5">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </span>
                    <h3 class="text-[13.5px] font-bold leading-snug text-slate-900">Informasi</h3>
                </div>

                <div class="space-y-3 p-4 sm:p-5">
                    {{-- Status pertemuan aktif --}}
                    @if ($activeMeeting)
                        <div class="flex gap-3 rounded-lg border border-blue-200 bg-blue-50/70 p-3.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-[12px] font-bold leading-snug text-blue-900">
                                    {{ $activeMeeting->status === 'ongoing' ? 'Pertemuan sedang berlangsung' : 'Formulir absensi sedang dibuka hari ini' }}
                                </p>
                                <p class="mt-0.5 text-[11.5px] leading-relaxed text-blue-800">{{ $activeMeeting->title }}</p>
                                <a href="{{ route('admin.pertemuan.show', $activeMeeting) }}" class="mt-1.5 inline-flex items-center gap-1 text-[11px] font-bold text-blue-700 hover:text-blue-900">
                                    Kelola pertemuan
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="flex gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3.5">
                            <svg class="mt-0.5 h-4 w-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="min-w-0">
                                <p class="text-[12px] font-bold leading-snug text-slate-800">Tidak ada pertemuan yang berlangsung saat ini.</p>
                                <p class="mt-0.5 text-[11.5px] leading-relaxed text-slate-500">
                                    Silakan cek kembali nanti atau lihat daftar pertemuan sebelumnya.
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- Ringkasan operasional nyata --}}
                    <dl class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                        @foreach ([
                            ['Formulir Terbit', number_format($publishedForms) . ' formulir', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                            ['Belum Hadir', number_format($stats['belum_hadir']) . ' peserta', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ] as $row)
                            <div class="flex items-center gap-2.5 rounded-lg border border-slate-200 bg-white px-3 py-2.5">
                                <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $row[2] }}"/>
                                </svg>
                                <div class="min-w-0">
                                    <dt class="truncate text-[10.5px] font-bold uppercase tracking-[0.05em] text-slate-500">{{ $row[0] }}</dt>
                                    <dd class="tabular truncate text-[12px] font-bold text-slate-900">{{ $row[1] }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>

                    {{-- Alur kerja ringkas --}}
                    <div class="rounded-lg border border-slate-200 bg-slate-50/60 p-3.5">
                        <p class="text-[10.5px] font-bold uppercase tracking-[0.08em] text-slate-500">Alur Kerja</p>
                        <ol class="mt-2 space-y-2">
                            @foreach ([
                                'Buat pertemuan dan tentukan jadwalnya.',
                                'Terbitkan formulir, lalu bagikan tautan atau QR Code.',
                                'Pantau kehadiran pada Rekap, unduh laporan resmi.',
                            ] as $index => $step)
                                <li class="flex gap-2.5">
                                    <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded bg-slate-900 text-[9.5px] font-bold text-white">{{ $index + 1 }}</span>
                                    <p class="text-[11px] leading-relaxed text-slate-600">{{ $step }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-layouts.admin>
