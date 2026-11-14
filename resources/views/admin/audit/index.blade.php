<x-layouts.admin title="Audit Log" heading="Audit Log">
    @php
        $toneByAction = function (string $action): string {
            return match (true) {
                str_starts_with($action, 'meeting_') => 'primary',
                str_starts_with($action, 'participant_') => 'info',
                str_starts_with($action, 'report_') => 'success',
                str_starts_with($action, 'attendance_') => 'warning',
                str_contains($action, 'deleted') => 'danger',
                default => 'default',
            };
        };

        $targetLabel = function (?string $type): string {
            if (! $type) {
                return 'Tidak ada';
            }

            return match ($type) {
                'App\\Models\\Meeting' => 'Pertemuan',
                'App\\Models\\Participant' => 'Peserta',
                'App\\Models\\Attendance' => 'Kehadiran',
                'App\\Models\\User' => 'Akun',
                default => class_basename($type),
            };
        };
    @endphp

    <div class="space-y-5 sm:space-y-6">
        <x-page-header
            eyebrow="Sistem"
            title="Audit Log"
            description="Jejak aktivitas sistem yang tercatat otomatis: aktor, aksi, objek terdampak, alamat IP, dan waktu kejadian. Catatan bersifat hanya-baca dan tidak dapat diubah atau dihapus dari panel."
        >
            <x-slot:actions>
                <span class="inline-flex min-h-[44px] items-center gap-2 rounded-lg border border-slate-200 bg-white px-3.5 text-[12.5px] font-semibold text-slate-700 shadow-sm">
                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span class="tabular">{{ number_format($totalLogs) }}</span>
                    <span class="font-medium text-slate-500">catatan</span>
                </span>
            </x-slot:actions>
        </x-page-header>

        {{-- Filter --}}
        <section class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm sm:p-5">
            <form method="GET" action="{{ route('admin.audit.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="min-w-0 flex-1">
                    <label for="q" class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.08em] text-slate-600">Cari</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/>
                            </svg>
                        </span>
                        <input
                            id="q"
                            name="q"
                            type="search"
                            value="{{ $search }}"
                            placeholder="Nama aktor, email, aksi, atau IP…"
                            class="h-11 w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 text-[13px] text-slate-900 placeholder:text-slate-500 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
                        >
                    </div>
                </div>

                <div class="sm:w-64">
                    <x-custom-select
                        label="Jenis Aksi"
                        id="action"
                        name="action"
                        :value="$actionFilter"
                        placeholder="Semua aksi"
                        :options="['' => 'Semua aksi'] + (is_array($actions) ? $actions : $actions->toArray())"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 text-[12.5px] font-bold text-white shadow-sm transition-colors hover:bg-blue-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-blue-500 focus-visible:ring-offset-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                        </svg>
                        <span>Terapkan</span>
                    </button>

                    @if ($search !== '' || $actionFilter !== '')
                        <a href="{{ route('admin.audit.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-3.5 text-[12.5px] font-semibold text-slate-700 transition-colors hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </section>

        {{-- Tabel --}}
        <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-3.5">
                <h3 class="text-[13px] font-bold text-slate-900">Jejak Aktivitas</h3>
                <p class="text-[11px] text-slate-500">Waktu ditampilkan dalam zona Asia/Jakarta (WIB).</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[880px] text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/80 text-[10px] uppercase tracking-[0.08em] text-slate-500">
                            <th class="px-4 py-2.5 font-bold">Waktu</th>
                            <th class="px-4 py-2.5 font-bold">Aktor</th>
                            <th class="px-4 py-2.5 font-bold">Aksi</th>
                            <th class="px-4 py-2.5 font-bold">Objek</th>
                            <th class="px-4 py-2.5 font-bold">Alamat IP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($logs as $log)
                            <tr class="transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-4 py-3.5">
                                    <p class="tabular font-semibold text-slate-800">{{ $log->created_at?->translatedFormat('d M Y') ?? '-' }}</p>
                                    <p class="tabular mt-0.5 text-[11px] text-slate-500">{{ $log->created_at?->format('H:i:s') ?? '' }} WIB</p>
                                </td>

                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-2.5">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-slate-900 text-[10px] font-bold text-white">
                                            {{ $log->user ? mb_strtoupper(mb_substr($log->user->name, 0, 1)) : '?' }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-semibold text-slate-900">{{ $log->user->name ?? 'Sistem / Anonim' }}</p>
                                            <p class="truncate text-[11px] text-slate-500">{{ $log->user->email ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-3.5">
                                    <x-badge :variant="$toneByAction($log->action)" size="sm" dot>
                                        {{ \App\Http\Controllers\Admin\AuditLogController::ACTION_LABELS[$log->action] ?? str_replace('_', ' ', $log->action) }}
                                    </x-badge>
                                    <p class="mt-1 font-mono text-[10px] text-slate-500">{{ $log->action }}</p>
                                </td>

                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-slate-800">{{ $targetLabel($log->target_type) }}</p>
                                    <p class="mt-0.5 font-mono text-[10px] text-slate-500">
                                        @if ($log->target_id)
                                            ID #{{ $log->target_id }}
                                        @else
                                            Tidak terikat objek
                                        @endif
                                    </p>
                                </td>

                                <td class="px-4 py-3.5">
                                    <span class="tabular font-mono text-[11px] text-slate-700">{{ $log->ip_address ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="mx-auto mb-3.5 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-500 ring-1 ring-inset ring-slate-200">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                        </svg>
                                    </div>
                                    <h4 class="text-[15px] font-bold text-slate-900">
                                        {{ $search !== '' || $actionFilter !== '' ? 'Tidak Ada Catatan yang Cocok' : 'Belum Ada Aktivitas Tercatat' }}
                                    </h4>
                                    <p class="mx-auto mt-1 max-w-md text-xs leading-relaxed text-slate-500">
                                        @if ($search !== '' || $actionFilter !== '')
                                            Ubah kata kunci pencarian atau pilih jenis aksi lain untuk melihat catatan yang tersedia.
                                        @else
                                            Jejak audit akan terisi otomatis begitu ada perubahan data pertemuan, peserta, kehadiran, ekspor laporan, atau pengaturan sistem.
                                        @endif
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($logs->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">
                    {{ $logs->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.admin>
