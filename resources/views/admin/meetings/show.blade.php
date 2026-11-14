<x-layouts.admin :title="$meeting->title" heading="Rincian Pertemuan">
    @php
        $publicUrl = url('/p/' . $meeting->public_token);
        $qrPreview = route('admin.pertemuan.qr', ['meeting' => $meeting, 'size' => 280, 'v' => 2]);
        $qrDownload = route('admin.pertemuan.qr.download', $meeting);
    @endphp

    <div class="space-y-5">
        {{-- Navigasi Balik & Judul Utama --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <a
                    href="{{ route('admin.pertemuan.index') }}"
                    class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-blue-600"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Kembali ke Data Pertemuan</span>
                </a>
                <h2 class="mt-1.5 text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">
                    {{ $meeting->title }}
                </h2>
                <div class="mt-2 flex flex-wrap items-center gap-2">
                    @if ($meeting->form_status === 'published')
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                            <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Formulir Dibuka (Menerima Kehadiran)
                        </span>
                    @elseif ($meeting->form_status === 'closed')
                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600 ring-1 ring-inset ring-slate-200">
                            Formulir Ditutup
                        </span>
                    @else
                        <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                            Formulir Draf (Belum Terbit)
                        </span>
                    @endif

                    <span class="inline-flex items-center rounded-md bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-700">
                        {{ $meeting->allow_anonymous ? 'Akses Bebas (Tanpa Login NIP)' : 'Wajib Login Akun NIP' }}
                    </span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Form Builder Button --}}
                <a
                    href="{{ route('admin.pertemuan.builder.index', $meeting) }}"
                    class="inline-flex min-h-[40px] items-center gap-2 rounded-lg bg-blue-600 px-4 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Buka Form Builder</span>
                </a>

                {{-- Toggle Form Status --}}
                <form method="POST" action="{{ route('admin.pertemuan.toggle-form', $meeting) }}">
                    @csrf
                    @if ($meeting->form_status === 'published')
                        <button
                            type="submit"
                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3.5 text-xs font-bold text-red-700 transition hover:bg-red-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Tutup Formulir</span>
                        </button>
                    @else
                        <button
                            type="submit"
                            class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-emerald-300 bg-emerald-50 px-3.5 text-xs font-bold text-emerald-800 transition hover:bg-emerald-100"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Buka / Terbitkan</span>
                        </button>
                    @endif
                </form>

                {{-- Ubah Pertemuan --}}
                <a
                    href="{{ route('admin.pertemuan.edit', $meeting) }}"
                    class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    <span>Ubah</span>
                </a>
            </div>
        </div>

        {{-- 2 Kolom Layout: Kiri Rincian & Navigasi, Kanan QR & Bagikan --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
            {{-- Kolom Kiri (2 Kolom di Desktop) --}}
            <div class="space-y-5 lg:col-span-2">
                {{-- Rincian Acara --}}
                <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-xs">
                    <h3 class="text-sm font-bold text-slate-900">Informasi Pelaksanaan</h3>
                    <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Tanggal &amp; Waktu</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $meeting->meeting_date->translatedFormat('l, d F Y') }}
                            </dd>
                            <dd class="mt-0.5 text-xs text-slate-600">
                                {{ substr($meeting->start_time, 0, 5) }} {{ $meeting->end_time ? '– ' . substr($meeting->end_time, 0, 5) : '' }} WIB
                            </dd>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3">
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Lokasi / Ruangan</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $meeting->location }}</dd>
                            <dd class="mt-0.5 text-xs text-slate-600">Penyelenggara: {{ $meeting->organizer }}</dd>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3">
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Batas Kuota Peserta</dt>
                            <dd class="mt-1 font-semibold text-slate-900">
                                {{ $meeting->max_participants ? number_format($meeting->max_participants) . ' Peserta' : 'Tidak Dibatasi' }}
                            </dd>
                        </div>

                        <div class="rounded-lg bg-slate-50 p-3">
                            <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Status Pelaksanaan</dt>
                            <dd class="mt-1 font-semibold text-slate-900">{{ $meeting->status_label }}</dd>
                        </div>

                        @if ($meeting->description)
                            <div class="rounded-lg bg-slate-50 p-3 sm:col-span-2">
                                <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Catatan / Deskripsi Agenda</dt>
                                <dd class="mt-1 whitespace-pre-line text-xs leading-relaxed text-slate-700">
                                    {{ $meeting->description }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                {{-- Akses Cepat ke Rekap & Laporan --}}
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <a
                        href="{{ route('admin.rekap.index', ['meeting_id' => $meeting->id]) }}"
                        class="flex items-center gap-3 rounded-xl border border-slate-200/90 bg-white p-4 shadow-xs transition hover:border-blue-400 hover:bg-blue-50/30"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Rekap Data Kehadiran</h4>
                            <p class="mt-0.5 text-[11px] text-slate-500">Pantau peserta hadir, jam presensi, &amp; paraf.</p>
                        </div>
                    </a>

                    <a
                        href="{{ route('admin.laporan.pdf', $meeting) }}"
                        class="flex items-center gap-3 rounded-xl border border-slate-200/90 bg-white p-4 shadow-xs transition hover:border-red-400 hover:bg-red-50/30"
                    >
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">Unduh Berita Acara PDF</h4>
                            <p class="mt-0.5 text-[11px] text-slate-500">Dokumen resmi ber-KOP SMKN 1 Majalengka.</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Kolom Kanan: QR Code & Pembagian Tautan --}}
            <div class="space-y-4">
                <div class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-xs text-center" x-data="{ copied: false }">
                    <h3 class="text-sm font-bold text-slate-900">QR Code Absensi</h3>
                    <p class="mt-0.5 text-[11px] text-slate-500">Tampilkan ke peserta untuk dipindai melalui HP</p>

                    <div class="my-4 inline-block rounded-xl border border-slate-200 bg-white p-3 shadow-xs">
                        <img
                            src="{{ $qrPreview }}"
                            alt="QR Code {{ $meeting->title }}"
                            class="h-48 w-48 object-contain"
                        >
                    </div>

                    {{-- Salin Tautan --}}
                    <div class="mb-3 text-left">
                        <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-500 mb-1">Tautan Absensi</label>
                        <div class="relative">
                            <input
                                id="public_meeting_url"
                                aria-label="Tautan formulir kehadiran publik"
                                type="text"
                                readonly
                                value="{{ $publicUrl }}"
                                class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-2.5 pr-20 font-mono text-xs text-slate-700"
                            >
                            <button
                                type="button"
                                @click="navigator.clipboard.writeText('{{ $publicUrl }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                class="absolute right-1 top-1 bottom-1 inline-flex items-center rounded-md bg-blue-600 px-3 text-[11px] font-bold text-white hover:bg-blue-700"
                            >
                                <span x-show="!copied">Salin</span>
                                <span x-show="copied" x-cloak class="text-emerald-200">Tersalin!</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-1">
                        <a
                            href="{{ route('admin.pertemuan.qr.print', $meeting) }}"
                            target="_blank"
                            class="col-span-2 inline-flex min-h-[38px] items-center justify-center gap-1.5 rounded-lg bg-blue-600 px-3 text-xs font-bold text-white shadow-xs hover:bg-blue-700"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            <span>Cetak Standee / Poster QR</span>
                        </a>

                        <a
                            href="{{ $qrDownload }}"
                            class="inline-flex min-h-[38px] items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span>Unduh SVG</span>
                        </a>

                        <a
                            href="{{ $publicUrl }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex min-h-[38px] items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-700 hover:bg-blue-100"
                        >
                            <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                            <span>Tes Buka Form</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
