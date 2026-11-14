<x-layouts.admin :title="'Peserta: ' . $meeting->title" heading="Kelola Peserta Pertemuan">
    <div class="space-y-6">
        {{-- Breadcrumb & Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <a
                    href="{{ route('admin.pertemuan.show', $meeting) }}"
                    class="mb-2 inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-[0.06em] text-slate-500 transition-colors hover:text-blue-600"
                >
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Rincian Pertemuan
                </a>

                <h2 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">
                    Daftar Undangan: {{ $meeting->title }}
                </h2>
                <p class="mt-0.5 text-xs text-slate-500">
                    {{ $meeting->meeting_date->translatedFormat('l, d F Y') }} · {{ $meeting->location }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <form
                    method="POST"
                    action="{{ route('admin.pertemuan.peserta.sync-all', $meeting) }}"
                    data-confirm="Undang seluruh peserta aktif ({{ $stats['total_available'] }} orang) ke agenda '{{ $meeting->title }}'?"
                    data-confirm-title="Undang Semua Peserta Aktif"
                    data-confirm-variant="primary"
                    data-confirm-btn="Undang Semua"
                >
                    @csrf
                    <x-button type="submit" variant="primary" size="sm" :disabled="$stats['total_available'] === 0">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                        <span>Undang Semua Peserta Aktif ({{ $stats['total_available'] }})</span>
                    </x-button>
                </form>

                <x-button :href="route('admin.rekap.index', ['meeting_id' => $meeting->id])" variant="outline" size="sm">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span>Rekap Kehadiran</span>
                </x-button>
            </div>
        </div>

        {{-- Ringkasan Metrik --}}
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500">Total Diundang</p>
                <p class="mt-1 text-2xl font-black text-slate-900">{{ number_format($stats['total_invited']) }}</p>
                <p class="mt-0.5 text-[11px] text-slate-500">Peserta terdaftar dalam agenda</p>
            </div>
            <div class="rounded-xl border border-emerald-200/90 bg-white p-4 shadow-sm">
                <p class="text-[10.5px] font-bold uppercase tracking-wider text-emerald-600">Sudah Hadir (Valid)</p>
                <p class="mt-1 text-2xl font-black text-emerald-700">{{ number_format($stats['total_attended']) }}</p>
                <p class="mt-0.5 text-[11px] text-slate-500">
                    {{ $stats['total_invited'] > 0 ? round(($stats['total_attended'] / $stats['total_invited']) * 100, 1) : 0 }}% tingkat kehadiran
                </p>
            </div>
            <div class="rounded-xl border border-blue-200/90 bg-white p-4 shadow-sm">
                <p class="text-[10.5px] font-bold uppercase tracking-wider text-blue-600">Belum Diundang</p>
                <p class="mt-1 text-2xl font-black text-blue-700">{{ number_format($stats['total_available']) }}</p>
                <p class="mt-0.5 text-[11px] text-slate-500">Peserta aktif yang tersedia</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
            {{-- Bagian Kiri: Tabel Peserta yang Sudah Diundang (7 Kolom) --}}
            <div class="space-y-4 lg:col-span-7">
                <div class="rounded-xl border border-slate-200/90 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-5 py-4">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Peserta Terdaftar dalam Agenda</h3>
                                <p class="text-xs text-slate-500">Daftar peserta yang wajib/dijadwalkan hadir.</p>
                            </div>

                            <form method="GET" action="{{ route('admin.pertemuan.peserta.index', $meeting) }}" class="flex items-center gap-2">
                                <input
                                    id="participants_search_q"
                                    aria-label="Cari nama, NIP, atau unit kerja peserta"
                                    type="text"
                                    name="q"
                                    value="{{ $search }}"
                                    placeholder="Cari NIP / nama..."
                                    class="h-8 rounded-lg border border-slate-300 px-2.5 text-xs text-slate-800 focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                >
                                <x-button type="submit" variant="secondary" size="sm">Cari</x-button>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="border-b border-slate-100 bg-slate-50/75 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <tr>
                                    <th class="px-4 py-3">Peserta</th>
                                    <th class="px-3 py-3">Unit Kerja</th>
                                    <th class="px-3 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($invitedParticipants as $item)
                                    @php
                                        $hasAttended = $item->pivot->status === 'attended' || $meeting->attendances()->where('nip', $item->nip)->exists();
                                    @endphp
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="font-semibold text-slate-900">{{ $item->name }}</p>
                                            <p class="text-[11px] font-mono text-slate-500">{{ $item->nip }}</p>
                                        </td>
                                        <td class="px-3 py-3 text-slate-600">
                                            {{ $item->unit_kerja ?: '-' }}
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($hasAttended)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-[10.5px] font-bold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                    Hadir
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2 py-0.5 text-[10.5px] font-bold text-amber-700 ring-1 ring-inset ring-amber-200">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                    Belum
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if (! $hasAttended)
                                                <form
                                                    method="POST"
                                                    action="{{ route('admin.pertemuan.peserta.destroy', [$meeting, $item]) }}"
                                                    data-confirm="Hapus undangan untuk {{ $item->name }} ({{ $item->nip }}) dari agenda pertemuan ini?"
                                                    data-confirm-title="Hapus Undangan Peserta"
                                                    data-confirm-variant="danger"
                                                    data-confirm-btn="Ya, Hapus"
                                                    class="inline"
                                                >
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-rose-600 hover:text-rose-800 font-semibold text-[11px]" title="Hapus dari daftar undangan">
                                                        Hapus
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-[10px] text-slate-400 italic">Terkunci (Hadir)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">
                                            Belum ada peserta yang diundang ke pertemuan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($invitedParticipants->hasPages())
                        <div class="border-t border-slate-100 p-3">
                            {{ $invitedParticipants->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- Bagian Kanan: Pilih & Tambahkan Peserta Baru (5 Kolom) --}}
            <div class="space-y-4 lg:col-span-5">
                <div class="rounded-xl border border-slate-200/90 bg-white shadow-sm" x-data="{ selectAll: false, selected: [] }">
                    <form method="POST" action="{{ route('admin.pertemuan.peserta.store', $meeting) }}">
                        @csrf
                        <div class="border-b border-slate-100 px-5 py-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Undang Peserta Baru</h3>
                                    <p class="text-xs text-slate-500">Pilih dari master data peserta aktif.</p>
                                </div>
                                <x-button type="submit" variant="primary" size="sm" ::disabled="selected.length === 0">
                                    Undang (<span x-text="selected.length">0</span>)
                                </x-button>
                            </div>
                        </div>

                        <div class="max-h-[500px] overflow-y-auto divide-y divide-slate-100">
                            @forelse ($availableParticipants as $p)
                                <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 cursor-pointer">
                                    <input
                                        id="chk_participant_{{ $p->id }}"
                                        aria-label="Pilih peserta {{ $p->name }}"
                                        type="checkbox"
                                        name="participant_ids[]"
                                        value="{{ $p->id }}"
                                        x-model="selected"
                                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                    >
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold text-slate-900 truncate">{{ $p->name }}</p>
                                        <p class="text-[10.5px] font-mono text-slate-500 truncate">{{ $p->nip }} · {{ $p->unit_kerja }}</p>
                                    </div>
                                </label>
                            @empty
                                <div class="px-4 py-8 text-center text-slate-400 text-xs">
                                    Semua peserta aktif sudah diundang ke pertemuan ini.
                                </div>
                            @endforelse
                        </div>

                        @if ($availableParticipants->hasPages())
                            <div class="border-t border-slate-100 p-3">
                                {{ $availableParticipants->links() }}
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
