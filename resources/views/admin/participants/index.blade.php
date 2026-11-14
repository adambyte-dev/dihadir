<x-layouts.admin title="Data Peserta" heading="Data Peserta">
    @php $hasFilter = request()->filled('search') || request()->filled('kabupaten_kota'); @endphp

    <div class="space-y-5 sm:space-y-6" x-data="{ showImportModal: false }">
        <x-page-header
            title="Data Peserta Terdaftar"
            description="Kelola identitas orang tua, tamu undangan, dan pegawai yang berhak mengisi absensi."
            eyebrow="Manajemen Peserta"
        >
            <x-slot:actions>
                <a
                    href="{{ route('admin.peserta.template-csv') }}"
                    class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 text-xs font-semibold text-slate-700 shadow-sm transition-colors hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    title="Unduh berkas contoh format CSV untuk impor peserta"
                >
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh Template CSV</span>
                </a>

                <x-button type="button" variant="outline" size="md" @click="showImportModal = true">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Impor CSV</span>
                </x-button>

                <x-button :href="route('admin.peserta.create')" variant="primary" size="md">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Tambah Peserta</span>
                </x-button>
            </x-slot:actions>
        </x-page-header>

        {{-- Pencarian --}}
        <section class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.peserta.index') }}" class="grid grid-cols-1 items-end gap-3 sm:grid-cols-12">
                <div class="sm:col-span-9">
                    <label for="search" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.06em] text-slate-600">Cari Peserta</label>
                    <div class="relative">
                        <input
                            type="text"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="NIP, nama lengkap, atau unit kerja…"
                            class="block w-full rounded-lg border border-slate-300 bg-white py-2.5 pl-9 pr-3 text-sm text-slate-900 shadow-sm transition placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                        <svg class="pointer-events-none absolute left-3 top-3 h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <div class="flex gap-2 sm:col-span-3">
                    <button type="submit" class="min-h-[44px] flex-1 rounded-lg bg-slate-800 px-4 text-xs font-bold text-white shadow-sm transition-colors hover:bg-slate-900">
                        Cari
                    </button>
                    @if ($hasFilter)
                        <a href="{{ route('admin.peserta.index') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-lg border border-slate-300 bg-white px-3 text-xs font-semibold text-slate-600 transition-colors hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Tabel --}}
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
            @if ($participants->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-[0.08em] text-slate-500">
                                <th class="w-12 px-4 py-3 text-center font-bold">No</th>
                                <th class="px-4 py-3 font-bold">Nama &amp; NIP</th>
                                <th class="px-4 py-3 font-bold">Unit Kerja</th>
                                <th class="px-4 py-3 font-bold">Wilayah</th>
                                <th class="px-4 py-3 text-center font-bold">Status</th>
                                <th class="px-4 py-3 text-right font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($participants as $index => $participant)
                                <tr class="transition-colors hover:bg-slate-50/70">
                                    <td class="tabular px-4 py-3.5 text-center font-mono text-slate-500">
                                        {{ $participants->firstItem() + $index }}
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold uppercase text-slate-600 ring-1 ring-inset ring-slate-200">
                                                {{ mb_substr($participant->name, 0, 1) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="truncate text-[13px] font-semibold text-slate-900">{{ $participant->name }}</p>
                                                <p class="font-mono text-[10.5px] text-slate-500">
                                                    {{ $participant->nip }}
                                                    @if ($participant->phone_number)
                                                        <span class="text-slate-300">•</span> {{ $participant->phone_number }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 text-slate-700">{{ $participant->unit_kerja }}</td>
                                    <td class="px-4 py-3.5 text-[11px] text-slate-600">{{ $participant->kabupaten_kota ?: '-' }}</td>
                                    <td class="px-4 py-3.5 text-center">
                                        @if ($participant->is_active)
                                            <x-badge variant="success" size="sm" dot>Aktif</x-badge>
                                        @else
                                            <x-badge variant="danger" size="sm" dot>Nonaktif</x-badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center justify-end gap-1">
                                            <a
                                                href="{{ route('admin.peserta.edit', $participant) }}"
                                                class="rounded-md p-1.5 text-slate-500 transition-colors hover:bg-amber-50 hover:text-amber-700"
                                                title="Ubah data peserta"
                                            >
                                                <span class="sr-only">Ubah {{ $participant->name }}</span>
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.peserta.destroy', $participant) }}"
                                                class="inline"
                                                data-confirm="Hapus data peserta '{{ $participant->name }}' ({{ $participant->nip }})? Riwayat kehadiran masa lalu yang sudah tercatat tetap tersimpan di database."
                                                data-confirm-title="Hapus Data Peserta"
                                                data-confirm-variant="danger"
                                                data-confirm-btn="Ya, Hapus"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md p-1.5 text-slate-500 transition-colors hover:bg-red-50 hover:text-red-600" title="Hapus peserta">
                                                    <span class="sr-only">Hapus {{ $participant->name }}</span>
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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

                @if ($participants->hasPages())
                    <div class="border-t border-slate-100 p-4">
                        {{ $participants->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-14 text-center">
                    <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-[15px] font-bold text-slate-900">
                        {{ $hasFilter ? 'Tidak Ada Peserta yang Cocok' : 'Belum Ada Data Peserta Terdaftar' }}
                    </h4>
                    <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-slate-500">
                        @if ($hasFilter)
                            Tidak ada peserta yang sesuai dengan kata kunci pencarian Anda.
                        @else
                            Sistem masih bersih. Tambahkan data peserta secara manual atau gunakan impor CSV untuk mengisi banyak data sekaligus.
                        @endif
                    </p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        @if ($hasFilter)
                            <a href="{{ route('admin.peserta.index') }}" class="inline-flex min-h-[44px] items-center rounded-lg border border-slate-300 bg-white px-3.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50">
                                Reset Pencarian
                            </a>
                        @else
                            <a href="{{ route('admin.peserta.create') }}" class="inline-flex min-h-[44px] items-center gap-1.5 rounded-lg bg-blue-600 px-3.5 text-xs font-bold text-white shadow-sm transition-colors hover:bg-blue-700">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Tambah Peserta
                            </a>
                            <button type="button" @click="showImportModal = true" class="inline-flex min-h-[44px] items-center rounded-lg border border-slate-300 bg-white px-3.5 text-xs font-bold text-slate-700 transition-colors hover:bg-slate-50">
                                Impor CSV
                            </button>
                        @endif
                    </div>
                </div>
            @endif
        </section>

        {{-- Modal impor CSV --}}
        <div
            x-show="showImportModal"
            x-cloak
            x-transition.opacity.duration.150ms
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm"
            @keydown.escape.window="showImportModal = false"
            role="dialog"
            aria-modal="true"
            aria-labelledby="import-modal-title"
        >
            <div class="w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl" @click.away="showImportModal = false">
                <div class="h-1 bg-blue-600" aria-hidden="true"></div>

                <div class="p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                            </span>
                            <div>
                                <h3 id="import-modal-title" class="text-[15px] font-bold text-slate-900">Impor Peserta dari CSV</h3>
                                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                                    Unggah berkas dengan urutan kolom:
                                    <code class="rounded bg-slate-100 px-1 py-0.5 font-mono text-[10.5px] text-slate-700">NIP, Nama, Unit Kerja, Kab/Kota, Telepon</code>
                                </p>
                            </div>
                        </div>

                        <button type="button" @click="showImportModal = false" class="rounded-md p-1 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-600">
                            <span class="sr-only">Tutup dialog</span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('admin.peserta.import') }}" enctype="multipart/form-data" class="mt-5">
                        @csrf
                        <label for="csv-file" class="mb-1.5 block text-[13px] font-semibold text-slate-700">Berkas CSV</label>
                        <input
                            type="file"
                            id="csv-file"
                            name="file"
                            accept=".csv,text/csv"
                            required
                            class="block w-full cursor-pointer rounded-lg border border-slate-300 bg-white text-xs text-slate-600 shadow-sm file:mr-3 file:cursor-pointer file:rounded-l-lg file:border-0 file:bg-slate-100 file:px-3.5 file:py-2.5 file:text-xs file:font-bold file:text-slate-700 hover:file:bg-slate-200"
                        >
                        <p class="mt-1.5 text-[11px] text-slate-500">Format .csv, maksimum 2 MB. Baris pertama dianggap sebagai header.</p>

                        <p class="mt-3 rounded-lg border border-amber-200 bg-amber-50 p-2.5 text-[11px] leading-relaxed text-amber-800">
                            Kata sandi awal setiap peserta otomatis diatur sama dengan NIP. Minta peserta menggantinya setelah login pertama.
                        </p>

                        <div class="mt-3 flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50/70 p-2.5 text-[11px] text-slate-600">
                            <span>Belum punya format berkas?</span>
                            <a
                                href="{{ route('admin.peserta.template-csv') }}"
                                class="inline-flex items-center gap-1 font-bold text-blue-600 hover:text-blue-700 hover:underline"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span>Unduh Template CSV</span>
                            </a>
                        </div>

                        <div class="mt-5 flex items-center justify-end gap-2">
                            <x-button type="button" variant="outline" size="sm" @click="showImportModal = false">Batal</x-button>
                            <x-button type="submit" variant="primary" size="sm">Unggah &amp; Impor</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
