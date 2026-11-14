<x-layouts.admin title="Kelola Akun Admin" heading="Kelola Admin">
    <div class="space-y-5 sm:space-y-6">
        {{-- Header + CTA --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <x-page-header
                title="Kelola Akun Pengelola"
                description="Daftar administrator dan super admin yang berwenang mengelola kegiatan di DIHADIR."
                eyebrow="Manajemen Sistem"
            />

            <x-button :href="route('admin.users.create')" variant="primary" class="w-full sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Tambah Admin</span>
            </x-button>
        </div>

        {{-- Ringkasan Metrik Akun --}}
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Akun</p>
                <p class="mt-1 text-2xl font-extrabold tabular text-slate-900">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Super Admin</p>
                <p class="mt-1 text-2xl font-extrabold tabular text-blue-600">{{ number_format($stats['super_admin']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Admin Acara</p>
                <p class="mt-1 text-2xl font-extrabold tabular text-slate-700">{{ number_format($stats['admin']) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Akun Aktif</p>
                <p class="mt-1 text-2xl font-extrabold tabular text-emerald-600">{{ number_format($stats['active']) }}</p>
            </div>
        </div>

        {{-- Filter & Pencarian --}}
        <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-12 sm:items-center">
                <div class="sm:col-span-6 lg:col-span-5">
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari nama, email, atau NIP..."
                            class="w-full rounded-lg border border-slate-300 bg-slate-50/50 py-2 pl-9 pr-3 text-xs text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-100"
                        >
                    </div>
                </div>

                <div class="sm:col-span-3 lg:col-span-3">
                    <x-custom-select
                        name="role"
                        :value="request('role')"
                        placeholder="Semua Peran"
                        :options="[
                            '' => 'Semua Peran',
                            'super_admin' => 'Super Admin',
                            'admin' => 'Admin Pengelola',
                        ]"
                        size="sm"
                        :auto-submit="true"
                    />
                </div>

                <div class="sm:col-span-3 lg:col-span-2">
                    <x-custom-select
                        name="status"
                        :value="request('status')"
                        placeholder="Semua Status"
                        :options="[
                            '' => 'Semua Status',
                            'active' => 'Aktif',
                            'inactive' => 'Nonaktif',
                        ]"
                        size="sm"
                        :auto-submit="true"
                    />
                </div>

                <div class="flex items-center gap-2 sm:col-span-12 lg:col-span-2 lg:justify-end">
                    <x-button type="submit" variant="primary" size="sm" class="w-full lg:w-auto">Filter</x-button>
                    @if (request()->hasAny(['search', 'role', 'status']))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Tabel Pengguna --}}
        <div class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/75 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                            <th class="px-4 py-3 sm:px-5">Nama Lengkap & NIP</th>
                            <th class="px-4 py-3 sm:px-5">Email</th>
                            <th class="px-4 py-3 sm:px-5">Peran</th>
                            <th class="px-4 py-3 sm:px-5">Status</th>
                            <th class="px-4 py-3 sm:px-5">Terdaftar</th>
                            <th class="px-4 py-3 text-right sm:px-5">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="transition-colors hover:bg-slate-50/80">
                                <td class="px-4 py-3.5 sm:px-5">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-8 w-8 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" loading="lazy" class="h-full w-full object-cover">
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                            <p class="font-mono text-[11px] text-slate-500">{{ $user->nip ?: 'NIP belum diisi' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-slate-700 sm:px-5 font-mono text-[11.5px]">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-3.5 sm:px-5">
                                    @if ($user->isSuperAdmin())
                                        <x-badge variant="primary" dot>Super Admin</x-badge>
                                    @else
                                        <x-badge variant="default">Admin Pengelola</x-badge>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 sm:px-5">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-bold text-slate-500">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-slate-500 sm:px-5">
                                    {{ $user->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 text-right sm:px-5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    title="{{ $user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}"
                                                    class="inline-flex h-7 items-center justify-center rounded-lg border border-slate-200 bg-white px-2 text-[11px] font-semibold text-slate-600 hover:bg-slate-50"
                                                >
                                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>
                                        @endif

                                        <a
                                            href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex h-7 items-center justify-center rounded-lg border border-slate-200 bg-white px-2.5 text-[11px] font-semibold text-blue-600 hover:bg-blue-50"
                                        >
                                            Ubah
                                        </a>

                                        @if ($user->id !== auth()->id())
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                data-confirm="Apakah Anda yakin ingin menghapus akun pengelola '{{ $user->name }}' ({{ $user->email }})? Tindakan ini tidak dapat dibatalkan."
                                                data-confirm-title="Hapus Akun Pengelola"
                                                data-confirm-variant="danger"
                                                data-confirm-btn="Ya, Hapus Akun"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    title="Hapus Akun"
                                                    class="inline-flex h-7 w-7 items-center justify-center rounded-lg border border-rose-200 bg-white text-rose-600 hover:bg-rose-50"
                                                >
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-500 sm:px-5">
                                    <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="font-semibold text-slate-700">Tidak ada akun admin yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Silakan sesuaikan filter atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-100 p-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>
