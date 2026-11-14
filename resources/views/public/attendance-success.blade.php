<x-layouts.guest :title="'Kehadiran Dicatat: ' . $meeting->title" max-width="max-w-2xl">
    <div class="mx-auto w-full max-w-md text-center">

        <div class="overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-sm">
            <div class="h-1 bg-emerald-600" aria-hidden="true"></div>

            <div class="px-6 py-8 sm:px-8">
                <div class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-200">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>

                <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-1 text-[10px] font-bold uppercase tracking-[0.1em] text-emerald-700 ring-1 ring-inset ring-emerald-200">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    Presensi Berhasil Diverifikasi
                </span>

                <h1 class="mt-2.5 text-[24px] font-extrabold leading-tight tracking-tight text-slate-900">
                    Kehadiran Berhasil Dicatat
                </h1>
                <p class="mx-auto mt-1.5 max-w-xs text-[13px] leading-relaxed text-slate-500">
                    Terima kasih. Data presensi dan tanda tangan digital Anda telah tersimpan resmi pada server DIHADIR.
                </p>

                <dl class="mt-6 space-y-2.5 rounded-xl border border-slate-200 bg-slate-50/80 p-4 text-left text-xs">
                    <div class="flex items-start justify-between gap-3 border-b border-slate-200/70 pb-2.5">
                        <dt class="shrink-0 font-medium text-slate-500">Kegiatan</dt>
                        <dd class="text-right font-bold leading-snug text-slate-900">{{ $meeting->title }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3 border-b border-slate-200/70 pb-2.5">
                        <dt class="shrink-0 font-medium text-slate-500">Nama Peserta</dt>
                        <dd class="text-right font-semibold text-slate-900">{{ $attendanceInfo['name'] }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3 border-b border-slate-200/70 pb-2.5">
                        <dt class="shrink-0 font-medium text-slate-500">Waktu Tercatat</dt>
                        <dd class="text-right font-mono font-semibold text-emerald-700">{{ $attendanceInfo['time'] }} WIB</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="shrink-0 font-medium text-slate-500">Status Kehadiran</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1.5 rounded-md bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                Hadir Sah
                            </span>
                        </dd>
                    </div>
                </dl>

                <p class="mt-4 flex items-start gap-1.5 text-left text-[11px] leading-relaxed text-slate-500">
                    <svg class="mt-px h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Simpan tangkapan layar halaman ini sebagai bukti sementara. Rekap resmi tersedia pada laporan pengelola.</span>
                </p>

                <div class="mt-5">
                    <a
                        href="{{ url('/') }}"
                        class="inline-flex min-h-[44px] w-full items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-4 text-[13px] font-bold text-slate-700 shadow-sm transition-colors hover:bg-slate-50"
                    >
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Selesai</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
