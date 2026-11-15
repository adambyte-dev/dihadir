<x-layouts.admin title="Laporan & Ekspor" heading="Laporan Kehadiran">
    <div class="space-y-5 sm:space-y-6">
        <x-page-header
            eyebrow="Data & Dokumen"
            title="Laporan Kehadiran"
            description="Unduh berita acara presensi resmi ber-KOP SMKN 1 Majalengka dalam format PDF, spreadsheet (CSV), atau cetak langsung dari browser."
        >
            <x-slot:actions>
                <a
                    href="{{ route('admin.rekap.index') }}"
                    class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-slate-300 bg-white px-3.5 text-[12.5px] font-semibold text-slate-700 shadow-sm transition-colors hover:border-slate-400 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Rekap Kehadiran</span>
                </a>
            </x-slot:actions>
        </x-page-header>

        {{-- Kartu penjelasan format dokumen --}}
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-xs">
            <div class="h-[2px] bg-blue-600" aria-hidden="true"></div>
            <div class="grid grid-cols-1 divide-y divide-slate-100 sm:grid-cols-3 sm:divide-x sm:divide-y-0">
                @foreach ([
                    [
                        'title' => 'PDF Berita Acara',
                        'desc' => 'Lengkap dengan kop sekolah, tabel peserta, kolom tanda tangan, dan lembar pengesahan kepala sekolah.',
                        'tone' => 'bg-rose-50 text-rose-600 ring-rose-100',
                        'path' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zM13 3v5a1 1 0 001 1h5',
                    ],
                    [
                        'title' => 'CSV / Excel',
                        'desc' => 'Kolom dinamis mengikuti versi formulir yang dipakai saat pengisian. Aman dari formula injection.',
                        'tone' => 'bg-emerald-50 text-emerald-600 ring-emerald-100',
                        'path' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                    ],
                    [
                        'title' => 'Cetak Langsung',
                        'desc' => 'Halaman print-friendly tanpa menu dashboard, siap dicetak pada kertas A4 atau disimpan sebagai PDF.',
                        'tone' => 'bg-slate-100 text-slate-600 ring-slate-200',
                        'path' => 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z',
                    ],
                ] as $format)
                    <div class="flex items-start gap-3 p-5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ring-1 ring-inset {{ $format['tone'] }}">
                            <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $format['path'] }}"/>
                            </svg>
                        </span>
                        <div class="min-w-0">
                            <p class="text-[13px] font-bold leading-snug text-slate-900">{{ $format['title'] }}</p>
                            <p class="mt-1 text-[11.5px] leading-relaxed text-slate-500">{{ $format['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Tabel pertemuan --}}
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-5 py-3.5">
                <div class="flex items-center gap-2.5">
                    <h3 class="text-[13px] font-bold text-slate-900">Daftar Dokumen Pertemuan</h3>
                    <span class="tabular rounded-md bg-slate-100 px-1.5 py-0.5 text-[11px] font-bold text-slate-600">{{ number_format($meetings->total()) }}</span>
                </div>
                <p class="text-[11px] text-slate-500">Laporan hanya memuat presensi berstatus valid.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[860px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-[0.08em] text-slate-500">
                            <th class="w-12 px-4 py-2.5 text-center font-bold">No</th>
                            <th class="px-4 py-2.5 font-bold">Kegiatan Pertemuan</th>
                            <th class="px-4 py-2.5 font-bold">Tanggal & Lokasi</th>
                            <th class="px-4 py-2.5 text-center font-bold">Total Hadir</th>
                            <th class="px-4 py-2.5 text-center font-bold">Status Formulir</th>
                            <th class="px-4 py-2.5 text-right font-bold">Ekspor & Cetak</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($meetings as $index => $meeting)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="tabular px-4 py-3.5 text-center font-mono text-slate-500">
                                    {{ $meetings->firstItem() + $index }}
                                </td>

                                <td class="px-4 py-3.5">
                                    <a
                                        href="{{ route('admin.pertemuan.show', $meeting) }}"
                                        class="block font-semibold leading-snug text-slate-900 transition-colors hover:text-blue-600"
                                    >
                                        {{ $meeting->title }}
                                    </a>
                                    <p class="mt-0.5 text-[11px] text-slate-500">{{ $meeting->organizer }}</p>
                                </td>

                                <td class="px-4 py-3.5">
                                    <p class="whitespace-nowrap font-semibold text-slate-800">{{ $meeting->meeting_date?->translatedFormat('d M Y') }}</p>
                                    <p class="mt-0.5 flex items-center gap-1 text-[11px] text-slate-500">
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        <span class="max-w-[190px] truncate">{{ $meeting->location }}</span>
                                    </p>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <span class="tabular inline-flex items-center gap-1.5 rounded-md border border-blue-200 bg-blue-50 px-2 py-1 text-[11px] font-bold text-blue-700">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ number_format($meeting->valid_attendances_count) }}
                                    </span>
                                </td>

                                <td class="px-4 py-3.5 text-center">
                                    <x-badge :variant="$meeting->form_status_badge_variant" size="sm" dot>{{ $meeting->form_status_label }}</x-badge>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5" x-data="{ showPdfModal: false }">
                                        {{-- Tombol Preview / Buka PDF Modal --}}
                                        <button
                                            type="button"
                                            @click="showPdfModal = true"
                                            title="Preview Berita Acara PDF"
                                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 text-[11px] font-bold text-blue-700 transition-colors hover:border-blue-300 hover:bg-blue-100 cursor-pointer"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Lihat PDF</span>
                                        </button>

                                        <a
                                            href="{{ route('admin.laporan.pdf', $meeting) }}"
                                            title="Unduh langsung berkas PDF"
                                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700 transition-colors hover:border-rose-300 hover:bg-rose-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-rose-400 focus-visible:ring-offset-2"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>Unduh</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.laporan.csv', $meeting) }}"
                                            title="Ekspor CSV / Excel"
                                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 text-[11px] font-bold text-emerald-700 transition-colors hover:border-emerald-300 hover:bg-emerald-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-400 focus-visible:ring-offset-2"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            <span>CSV</span>
                                        </a>

                                        <a
                                            href="{{ route('admin.laporan.print', $meeting) }}"
                                            target="_blank"
                                            rel="noopener"
                                            title="Cetak via browser"
                                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-2.5 text-[11px] font-bold text-slate-700 transition-colors hover:border-slate-300 hover:bg-slate-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Cetak</span>
                                        </a>

                                        {{-- MODAL PREVIEW PDF INTERAKTIF --}}
                                        <div
                                            x-show="showPdfModal"
                                            x-cloak
                                            x-transition.opacity.duration.200ms
                                            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 p-4 sm:p-6 backdrop-blur-xs"
                                            @keydown.escape.window="showPdfModal = false"
                                        >
                                            <div 
                                                class="flex h-full max-h-[92vh] w-full max-w-5xl flex-col overflow-hidden rounded-xl border border-slate-700 bg-white shadow-md"
                                                @click.away="showPdfModal = false"
                                            >
                                                {{-- Modal Header --}}
                                                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-900 px-5 py-3 text-white">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 text-white shadow-xs">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                            </svg>
                                                        </span>
                                                        <div class="text-left">
                                                            <h4 class="text-xs font-bold text-white line-clamp-1">Preview Berita Acara: {{ $meeting->title }}</h4>
                                                            <p class="text-[10px] text-slate-400">Format Resmi KOP SMKN 1 Majalengka</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <a
                                                            href="{{ route('admin.laporan.pdf', $meeting) }}"
                                                            class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-xs hover:bg-blue-700"
                                                        >
                                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                            </svg>
                                                            <span>Unduh PDF</span>
                                                        </a>

                                                        <button
                                                            type="button"
                                                            @click="showPdfModal = false"
                                                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-white/10 hover:text-white"
                                                        >
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                {{-- Modal Iframe PDF Body --}}
                                                <div class="relative flex-1 bg-slate-100">
                                                    <template x-if="showPdfModal">
                                                        <iframe
                                                            :src="'{{ route('admin.laporan.pdf', $meeting) }}?preview=1'"
                                                            class="h-full w-full border-0"
                                                            title="PDF Preview"
                                                        ></iframe>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-[15px] font-bold text-slate-900">Belum Ada Dokumen Laporan</h4>
                                    <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-slate-500">
                                        Laporan dibuat dari data pertemuan. Buat pertemuan terlebih dahulu, lalu terbitkan formulir presensinya agar laporan dapat diunduh.
                                    </p>
                                    <div class="mt-4">
                                        <a
                                            href="{{ route('admin.pertemuan.create') }}"
                                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            Buat Pertemuan
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($meetings->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $meetings->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
