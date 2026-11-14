<x-layouts.admin title="Rekap Kehadiran" heading="Rekap Kehadiran">
    @php
        $statCards = [
            ['label' => 'Sudah Hadir', 'value' => $stats['total_attended'], 'caption' => 'Tervalidasi di sistem', 'accent' => 'bg-emerald-500', 'text' => 'text-emerald-600', 'ring' => 'bg-emerald-50 text-emerald-600 ring-emerald-100', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Total Diundang', 'value' => $stats['total_invited'], 'caption' => 'Peserta terjadwal', 'accent' => 'bg-slate-500', 'text' => 'text-slate-900', 'ring' => 'bg-slate-100 text-slate-600 ring-slate-200', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
            ['label' => 'Belum Hadir', 'value' => $stats['total_absent'], 'caption' => 'Belum mengisi formulir', 'accent' => 'bg-amber-500', 'text' => 'text-amber-600', 'ring' => 'bg-amber-50 text-amber-600 ring-amber-100', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Persentase', 'value' => $stats['attendance_rate'], 'caption' => 'Tingkat kehadiran', 'accent' => 'bg-blue-500', 'text' => 'text-blue-600', 'ring' => 'bg-blue-50 text-blue-600 ring-blue-100', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'suffix' => '%'],
        ];
    @endphp

    <div class="space-y-5 sm:space-y-6" x-data="{ signatureModalOpen: false, modalSigUrl: '', modalSigName: '' }">
        <x-page-header
            title="Rekap Kehadiran Peserta"
            description="Pantau kehadiran per pertemuan, validasi status, dan periksa tanda tangan digital."
            eyebrow="Data & Dokumen"
        />

        {{-- Pemilih pertemuan + ekspor --}}
        <section class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                @php
                    $meetingOptions = [];
                    foreach ($meetings as $m) {
                        $meetingOptions[$m->id] = $m->title . ' (' . ($m->meeting_date?->format('d/m/Y') ?? '-') . ')';
                    }
                @endphp
                <form method="GET" action="{{ route('admin.rekap.index') }}" id="filterMeetingForm" class="w-full lg:max-w-md">
                    <x-custom-select
                        label="Pilih Kegiatan Pertemuan"
                        name="meeting_id"
                        id="meeting_id"
                        :value="$selectedMeeting?->id"
                        :options="$meetingOptions"
                        placeholder="-- Pilih Pertemuan --"
                        :auto-submit="true"
                    />
                </form>

                @if ($selectedMeeting)
                    <div class="flex flex-wrap items-center gap-2">
                        <a
                            href="{{ route('admin.laporan.pdf', $selectedMeeting) }}"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-red-200 bg-red-50 px-3 text-xs font-bold text-red-700 transition-colors hover:bg-red-100"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            PDF
                        </a>

                        <a
                            href="{{ route('admin.laporan.csv', $selectedMeeting) }}"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-700 transition-colors hover:bg-emerald-100"
                        >
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            CSV
                        </a>

                        <a
                            href="{{ route('admin.laporan.print', $selectedMeeting) }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50"
                        >
                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                            </svg>
                            Cetak
                        </a>
                    </div>
                @endif
            </div>
        </section>

        @if ($selectedMeeting)
            {{-- Statistik --}}
            <div class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
                @foreach ($statCards as $card)
                    <div class="relative overflow-hidden rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                        <div class="absolute inset-x-0 top-0 h-[3px] {{ $card['accent'] }}" aria-hidden="true"></div>
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.08em] text-slate-500">{{ $card['label'] }}</p>
                                <p class="tabular mt-1.5 text-[26px] font-extrabold leading-none tracking-tight {{ $card['text'] }}">
                                    {{ $card['label'] === 'Persentase' ? $card['value'] : number_format($card['value']) }}{{ $card['suffix'] ?? '' }}
                                </p>
                            </div>
                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg ring-1 ring-inset {{ $card['ring'] }}">
                                <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                                </svg>
                            </span>
                        </div>
                        <p class="mt-2 text-[11px] font-medium text-slate-500">{{ $card['caption'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Tabel --}}
            <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50 p-0.5" role="tablist">
                        <a
                            href="{{ route('admin.rekap.index', ['meeting_id' => $selectedMeeting->id, 'tab' => 'hadir', 'q' => $search]) }}"
                            role="tab"
                            aria-selected="{{ $tab === 'hadir' ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11.5px] font-bold transition-colors {{ $tab === 'hadir' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                            Sudah Hadir
                            <span class="tabular rounded bg-slate-100 px-1 text-[10px] text-slate-600">{{ $stats['total_attended'] }}</span>
                        </a>

                        <a
                            href="{{ route('admin.rekap.index', ['meeting_id' => $selectedMeeting->id, 'tab' => 'belum_hadir', 'q' => $search]) }}"
                            role="tab"
                            aria-selected="{{ $tab === 'belum_hadir' ? 'true' : 'false' }}"
                            class="inline-flex items-center gap-1.5 rounded-md px-3 py-1.5 text-[11.5px] font-bold transition-colors {{ $tab === 'belum_hadir' ? 'bg-white text-slate-900 shadow-sm ring-1 ring-slate-200' : 'text-slate-500 hover:text-slate-800' }}"
                        >
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500" aria-hidden="true"></span>
                            Belum Hadir
                            <span class="tabular rounded bg-slate-100 px-1 text-[10px] text-slate-600">{{ $stats['total_absent'] }}</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('admin.rekap.index') }}" class="flex w-full items-center gap-2 sm:w-auto">
                        <input type="hidden" name="meeting_id" value="{{ $selectedMeeting->id }}">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        <div class="relative w-full sm:w-64">
                            <input
                                type="text"
                                name="q"
                                value="{{ $search }}"
                                placeholder="Cari nama, NIP, instansi…"
                                class="w-full rounded-lg border border-slate-300 bg-white py-1.5 pl-8 pr-3 text-xs text-slate-900 shadow-sm transition placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                            >
                            <svg class="pointer-events-none absolute left-2.5 top-2 h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        @if ($search)
                            <a href="{{ route('admin.rekap.index', ['meeting_id' => $selectedMeeting->id, 'tab' => $tab]) }}" class="shrink-0 text-[11px] font-semibold text-slate-500 transition-colors hover:text-slate-800">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>

                @if ($tab === 'hadir')
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-[0.08em] text-slate-500">
                                    <th class="w-12 px-4 py-3 text-center font-bold">No</th>
                                    <th class="px-4 py-3 font-bold">Nama &amp; NIP</th>
                                    <th class="px-4 py-3 font-bold">Unit Kerja &amp; Wilayah</th>
                                    <th class="px-4 py-3 font-bold">Waktu</th>
                                    <th class="px-4 py-3 text-center font-bold">Tanda Tangan</th>
                                    <th class="px-4 py-3 text-center font-bold">Status</th>
                                    <th class="px-4 py-3 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($attendances as $index => $item)
                                    <tr class="transition-colors hover:bg-slate-50/70">
                                        <td class="tabular px-4 py-3 text-center font-mono text-slate-500">
                                            {{ $attendances->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="text-[13px] font-semibold text-slate-900">{{ $item->name }}</p>
                                            <p class="font-mono text-[10.5px] text-slate-500">{{ $item->nip }}</p>
                                            @if ($item->customAnswers->isNotEmpty())
                                                <div class="mt-1 space-y-0.5 border-t border-slate-100 pt-1">
                                                    @foreach ($item->customAnswers as $ca)
                                                        <p class="text-[10px] text-slate-600">
                                                            <span class="font-bold text-slate-700">{{ $ca->field_label }}:</span>
                                                            @if ($ca->file_path)
                                                                <a
                                                                    href="{{ route('admin.rekap.attachment', $ca) }}"
                                                                    target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="font-semibold text-blue-700 underline decoration-blue-300 underline-offset-2 transition-colors hover:text-blue-900"
                                                                >{{ $ca->answer_value ?: 'Buka Lampiran' }}</a>
                                                            @else
                                                                <span>{{ $ca->answer_value }}</span>
                                                            @endif
                                                        </p>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-slate-800">{{ $item->unit_kerja }}</p>
                                            <p class="text-[11px] text-slate-500">{{ $item->kabupaten_kota ?? '-' }}</p>
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            <p class="tabular font-semibold text-slate-800">{{ substr($item->attended_time, 0, 5) }} WIB</p>
                                            <p class="text-[10px] text-slate-500">{{ $item->attended_date?->format('d/m/Y') }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($item->signature_path)
                                                <button
                                                    type="button"
                                                    @click="modalSigUrl = '{{ route('admin.rekap.signature', $item) }}'; modalSigName = @js($item->name); signatureModalOpen = true"
                                                    class="inline-block rounded-md border border-slate-200 bg-white p-1 shadow-sm transition-colors hover:border-blue-400"
                                                    title="Lihat tanda tangan penuh"
                                                >
                                                    <img
                                                        src="{{ route('admin.rekap.signature', $item) }}"
                                                        alt="Tanda tangan {{ $item->name }}"
                                                        class="h-6 w-14 object-contain"
                                                        loading="lazy"
                                                    >
                                                </button>
                                            @else
                                                <span class="text-[10px] italic text-slate-500">Tidak ada</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if ($item->status === 'valid')
                                                <x-badge variant="success" size="sm" dot>Valid</x-badge>
                                            @elseif ($item->status === 'flagged')
                                                <x-badge variant="warning" size="sm" dot>Ditandai</x-badge>
                                            @else
                                                <x-badge variant="danger" size="sm" dot>Dibatalkan</x-badge>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <form method="POST" action="{{ route('admin.rekap.status', $item) }}" class="inline">
                                                @csrf
                                                @if ($item->status !== 'valid')
                                                    <input type="hidden" name="status" value="valid">
                                                    <button type="submit" class="rounded-md border border-emerald-200 bg-emerald-50 px-2 py-1 text-[10.5px] font-bold text-emerald-700 transition-colors hover:bg-emerald-100" title="Validasi kehadiran ini">
                                                        Validasi
                                                    </button>
                                                @else
                                                    <input type="hidden" name="status" value="flagged">
                                                    <button type="submit" class="rounded-md border border-amber-200 bg-amber-50 px-2 py-1 text-[10.5px] font-bold text-amber-700 transition-colors hover:bg-amber-100" title="Tandai mencurigakan">
                                                        Tandai
                                                    </button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="mx-auto mb-2.5 flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-500">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <p class="text-[13px] font-bold text-slate-800">Belum Ada Peserta yang Hadir</p>
                                            <p class="mt-0.5 text-[11px] text-slate-500">Belum ada peserta yang mengisi formulir kehadiran pada kegiatan ini.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($attendances->hasPages())
                        <div class="border-t border-slate-100 p-4">{{ $attendances->links() }}</div>
                    @endif
                @endif

                @if ($tab === 'belum_hadir')
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-xs">
                            <thead>
                                <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-[0.08em] text-slate-500">
                                    <th class="w-12 px-4 py-3 text-center font-bold">No</th>
                                    <th class="px-4 py-3 font-bold">Nama Peserta</th>
                                    <th class="px-4 py-3 font-bold">NIP</th>
                                    <th class="px-4 py-3 font-bold">Unit Kerja</th>
                                    <th class="px-4 py-3 font-bold">Kontak</th>
                                    <th class="px-4 py-3 text-center font-bold">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($unattendedParticipants as $index => $participant)
                                    <tr class="transition-colors hover:bg-slate-50/70">
                                        <td class="tabular px-4 py-3 text-center font-mono text-slate-500">
                                            {{ $unattendedParticipants->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3 text-[13px] font-semibold text-slate-900">{{ $participant->name }}</td>
                                        <td class="px-4 py-3 font-mono text-slate-600">{{ $participant->nip }}</td>
                                        <td class="px-4 py-3 text-slate-700">{{ $participant->unit_kerja }}</td>
                                        <td class="px-4 py-3 text-slate-600">{{ $participant->phone_number ?? '-' }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <x-badge variant="warning" size="sm" dot>Belum Hadir</x-badge>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="mx-auto mb-2.5 flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 text-emerald-500">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <p class="text-[13px] font-bold text-slate-800">Seluruh Peserta Terundang Telah Hadir</p>
                                            <p class="mt-0.5 text-[11px] text-slate-500">Tidak ada peserta terdaftar yang tercatat belum mengisi kehadiran.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($unattendedParticipants->hasPages())
                        <div class="border-t border-slate-100 p-4">{{ $unattendedParticipants->links() }}</div>
                    @endif
                @endif
            </section>
        @else
            <div class="rounded-xl border border-slate-200/90 bg-white px-6 py-14 text-center shadow-sm">
                <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-[15px] font-bold text-slate-900">Belum Ada Kegiatan Pertemuan</h3>
                <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-slate-500">
                    Buat kegiatan pertemuan terlebih dahulu di menu Data Pertemuan untuk mulai memantau rekap absensi.
                </p>
                <div class="mt-4">
                    <a href="{{ route('admin.pertemuan.create') }}" class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Buat Pertemuan Baru
                    </a>
                </div>
            </div>
        @endif

        {{-- Modal tanda tangan --}}
        <div
            x-show="signatureModalOpen"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @keydown.escape.window="signatureModalOpen = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sig-modal-title"
        >
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl" @click.away="signatureModalOpen = false">
                <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div>
                        <h4 id="sig-modal-title" class="text-[14px] font-bold text-slate-900">Tanda Tangan Digital</h4>
                        <p class="mt-0.5 text-[11px] text-slate-500" x-text="modalSigName"></p>
                    </div>
                    <button type="button" @click="signatureModalOpen = false" class="rounded-md p-1 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-600">
                        <span class="sr-only">Tutup</span>
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5">
                    <div class="flex items-center justify-center rounded-xl border border-dashed border-slate-300 bg-slate-50 py-6">
                        <img :src="modalSigUrl" alt="Tanda tangan digital" class="max-h-48 max-w-full object-contain">
                    </div>

                    <div class="mt-4 flex justify-end">
                        <button type="button" @click="signatureModalOpen = false" class="min-h-[44px] rounded-lg border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
