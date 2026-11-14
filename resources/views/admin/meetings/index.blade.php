<x-layouts.admin title="Data Pertemuan" heading="Data Pertemuan">
    @php
        $hasFilter = request()->hasAny(['search', 'status', 'form_status']);
        $totalMeetings = $meetings->total();
    @endphp

    <div class="space-y-5">
        {{-- Header Bersih & Aksi Utama --}}
        <div class="flex flex-col gap-3.5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">Data Pertemuan</h2>
                <p class="mt-0.5 text-xs text-slate-500">Kelola agenda rapat, formulir presensi digital, dan penerbitan QR Code.</p>
            </div>
            <div class="flex items-center gap-2">
                <a
                    href="{{ route('admin.pertemuan.create') }}"
                    class="inline-flex min-h-[42px] items-center gap-2 rounded-lg bg-blue-600 px-4 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Buat Pertemuan Baru</span>
                </a>
            </div>
        </div>

        {{-- Filter Ringkas & Cepat --}}
        <div class="rounded-xl border border-slate-200/90 bg-white p-3.5 shadow-xs">
            <form method="GET" action="{{ route('admin.pertemuan.index') }}" class="flex flex-col gap-2.5 sm:flex-row sm:items-center">
                {{-- Quick Search --}}
                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama pertemuan, lokasi, atau penyelenggara..."
                        class="block w-full rounded-lg border border-slate-300 bg-white py-2 pl-9 pr-3 text-xs text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    >
                    <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                {{-- Status Acara --}}
                <div class="w-full sm:w-48">
                    <x-custom-select
                        name="status"
                        placeholder="Semua Status Acara"
                        :value="request('status')"
                        :options="[
                            '' => 'Semua Status Acara',
                            'ongoing' => 'Sedang Berlangsung',
                            'scheduled' => 'Terjadwal',
                            'completed' => 'Selesai',
                            'draft' => 'Draf',
                        ]"
                        size="sm"
                    />
                </div>

                {{-- Status Formulir --}}
                <div class="w-full sm:w-44">
                    <x-custom-select
                        name="form_status"
                        placeholder="Semua Formulir"
                        :value="request('form_status')"
                        :options="[
                            '' => 'Semua Formulir',
                            'published' => 'Terbit (Buka)',
                            'closed' => 'Ditutup',
                            'draft' => 'Draf',
                        ]"
                        size="sm"
                    />
                </div>

                <div class="flex items-center gap-1.5">
                    <button
                        type="submit"
                        class="inline-flex min-h-[38px] items-center justify-center rounded-lg bg-slate-800 px-3.5 text-xs font-semibold text-white transition-colors hover:bg-slate-900"
                    >
                        Filter
                    </button>
                    @if ($hasFilter)
                        <a
                            href="{{ route('admin.pertemuan.index') }}"
                            class="inline-flex min-h-[38px] items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-600 hover:bg-slate-50"
                        >
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Kegiatan Minimalis & Jelas --}}
        <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-xs">
            @if ($meetings->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                <th class="px-4 py-3">Nama Kegiatan</th>
                                <th class="px-4 py-3">Jadwal &amp; Ruangan</th>
                                <th class="px-4 py-3 text-center">Status Formulir</th>
                                <th class="px-4 py-3 text-center">Akses</th>
                                <th class="px-4 py-3 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($meetings as $meeting)
                                <tr class="transition-colors hover:bg-slate-50/60">
                                    {{-- Kolom 1: Nama Kegiatan --}}
                                    <td class="px-4 py-3.5">
                                        <a href="{{ route('admin.pertemuan.show', $meeting) }}" class="block font-bold text-slate-900 hover:text-blue-600 sm:text-[13px]">
                                            {{ $meeting->title }}
                                        </a>
                                        <div class="mt-1 flex items-center gap-2 text-[11px] text-slate-500">
                                            <span>{{ $meeting->organizer }}</span>
                                            <span class="text-slate-300">•</span>
                                            <span class="font-mono text-[10px] text-slate-400">ID: {{ substr($meeting->public_token, 0, 8) }}</span>
                                        </div>
                                    </td>

                                    {{-- Kolom 2: Jadwal & Ruangan --}}
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        <div class="flex items-center gap-1.5 font-semibold text-slate-800">
                                            <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>{{ $meeting->meeting_date->translatedFormat('d M Y') }}</span>
                                        </div>
                                        <div class="mt-0.5 text-[11px] text-slate-500">
                                            {{ substr($meeting->start_time, 0, 5) }} WIB • {{ $meeting->location }}
                                        </div>
                                    </td>

                                    {{-- Kolom 3: Status Formulir --}}
                                    <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                        @if ($meeting->form_status === 'published')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Terbit (Buka)
                                            </span>
                                        @elseif ($meeting->form_status === 'closed')
                                            <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-0.5 text-[11px] font-semibold text-slate-600 ring-1 ring-inset ring-slate-200">
                                                Ditutup
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                Draf
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom 4: Akses --}}
                                    <td class="whitespace-nowrap px-4 py-3.5 text-center">
                                        @if ($meeting->allow_anonymous)
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-[10.5px] font-medium text-slate-700">
                                                Terbuka
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-md bg-blue-50 px-2 py-0.5 text-[10.5px] font-semibold text-blue-700">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Wajib NIP
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Kolom 5: Aksi Utama --}}
                                    <td class="whitespace-nowrap px-4 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            {{-- Form Builder Button (Utama) --}}
                                            <a
                                                href="{{ route('admin.pertemuan.builder.index', $meeting) }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-blue-200 bg-blue-50 px-2.5 py-1.5 text-xs font-bold text-blue-700 transition hover:bg-blue-100"
                                                title="Desain Formulir Absensi"
                                            >
                                                <svg class="h-3.5 w-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span>Form Builder</span>
                                            </a>

                                            {{-- QR & Rincian --}}
                                            <a
                                                href="{{ route('admin.pertemuan.show', $meeting) }}"
                                                class="inline-flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                                title="Lihat Rincian & QR Code"
                                            >
                                                <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                                </svg>
                                                <span>QR &amp; Rincian</span>
                                            </a>

                                            {{-- Ubah --}}
                                            <a
                                                href="{{ route('admin.pertemuan.edit', $meeting) }}"
                                                class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                                                title="Ubah Jadwal / Pengaturan"
                                            >
                                                <span class="sr-only">Ubah {{ $meeting->title }}</span>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                                </svg>
                                            </a>

                                            {{-- Duplikasi --}}
                                            <form
                                                method="POST"
                                                action="{{ route('admin.pertemuan.duplicate', $meeting) }}"
                                                class="inline"
                                                data-confirm="Gandakan agenda pertemuan ini? Semua pengaturan formulir dan pertanyaan akan disalin ke draf pertemuan baru."
                                                data-confirm-title="Gandakan Pertemuan"
                                                data-confirm-variant="primary"
                                                data-confirm-btn="Gandakan Sekarang"
                                            >
                                                @csrf
                                                <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700" title="Gandakan Pertemuan">
                                                    <span class="sr-only">Gandakan</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                    </svg>
                                                </button>
                                            </form>

                                            {{-- Hapus --}}
                                            <form
                                                method="POST"
                                                action="{{ route('admin.pertemuan.destroy', $meeting) }}"
                                                class="inline"
                                                data-confirm="Apakah Anda yakin ingin menghapus agenda pertemuan '{{ $meeting->title }}'? Pertemuan akan dipindahkan ke arsip sampah dan formulir tidak dapat diakses lagi."
                                                data-confirm-title="Hapus Pertemuan"
                                                data-confirm-variant="danger"
                                                data-confirm-btn="Ya, Hapus"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-600" title="Hapus">
                                                    <span class="sr-only">Hapus</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($meetings->hasPages())
                    <div class="border-t border-slate-100 p-3.5">
                        {{ $meetings->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ $hasFilter ? 'Tidak Ada Pertemuan yang Cocok' : 'Belum Ada Kegiatan Pertemuan' }}
                    </h3>
                    <p class="mx-auto mt-1 max-w-sm text-xs text-slate-500">
                        {{ $hasFilter ? 'Coba ubah kata kunci pencarian atau reset filter di atas.' : 'Belum ada kegiatan pertemuan yang terdaftar pada sistem. Silakan buat kegiatan baru.' }}
                    </p>
                    <div class="mt-4">
                        @if ($hasFilter)
                            <a href="{{ route('admin.pertemuan.index') }}" class="inline-flex min-h-[38px] items-center rounded-lg border border-slate-300 bg-white px-3.5 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                Reset Filter
                            </a>
                        @else
                            <a href="{{ route('admin.pertemuan.create') }}" class="inline-flex min-h-[40px] items-center gap-1.5 rounded-lg bg-blue-600 px-4 text-xs font-bold text-white hover:bg-blue-700">
                                + Buat Pertemuan Baru
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
