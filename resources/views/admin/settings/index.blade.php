<x-layouts.admin title="Pengaturan Sistem" heading="Pengaturan">
    @php
        $tabs = [
            ['key' => 'profil', 'label' => 'Profil & Keamanan', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
        ];

        if (auth()->user()->isSuperAdmin()) {
            $tabs[] = ['key' => 'sistem', 'label' => 'Identitas Sekolah', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'];
        }

        $tabs[] = ['key' => 'info', 'label' => 'Informasi Server', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01'];

        $initial = strtoupper(substr($user->name ?? 'A', 0, 1));
    @endphp

    <div class="space-y-5 sm:space-y-6">
        <x-page-header
            eyebrow="Sistem"
            title="Pengaturan"
            description="Kelola profil pengelola, identitas lembaga untuk kop laporan, serta pantau kondisi lingkungan server."
        />

        {{-- Segmented tabs --}}
        <div class="overflow-x-auto">
            <nav
                class="inline-flex min-w-full gap-0.5 rounded-lg border border-slate-200 bg-slate-50 p-1 sm:min-w-0"
                aria-label="Navigasi pengaturan"
            >
                @foreach ($tabs as $item)
                    @php $isActive = $tab === $item['key']; @endphp
                    <a
                        href="{{ route('admin.pengaturan.index', ['tab' => $item['key']]) }}"
                        @if ($isActive) aria-current="page" @endif
                        class="inline-flex min-h-[44px] flex-1 items-center justify-center gap-1.5 whitespace-nowrap rounded-md px-3 text-[12px] font-semibold transition-all sm:flex-none {{ $isActive ? 'bg-white text-slate-900 shadow-sm ring-1 ring-inset ring-slate-200' : 'text-slate-500 hover:bg-white/60 hover:text-slate-800' }}"
                    >
                        <svg class="h-3.5 w-3.5 {{ $isActive ? 'text-blue-600' : 'text-slate-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="{{ $item['icon'] }}"/>
                        </svg>
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- ============ TAB: PROFIL ============ --}}
        @if ($tab === 'profil')
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_300px] lg:gap-6">
                <div class="space-y-5">
                    <form method="POST" action="{{ route('admin.pengaturan.profile') }}" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <x-form-section
                            title="Data Profil Pengelola"
                            description="Informasi ini tampil pada sidebar dan jejak audit sistem."
                            icon="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                        >
                            <div class="space-y-4">
                                <x-input
                                    label="Nama Lengkap"
                                    name="name"
                                    :value="old('name', $user->name)"
                                    required
                                />

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <x-input
                                        label="Nomor Induk Pegawai (NIP)"
                                        name="nip"
                                        :value="old('nip', $user->nip)"
                                        placeholder="198001012005011003"
                                    />

                                    <x-input
                                        label="Alamat Email"
                                        type="email"
                                        name="email"
                                        :value="old('email', $user->email)"
                                        required
                                    />
                                </div>
                            </div>
                        </x-form-section>

                        {{-- Section Avatar Profil (DiceBear) --}}
                        <div x-data="{
                            selectedStyle: '{{ old('avatar_style', $user->avatar_style ?: 'lorelei') }}',
                            avatarSeed: '{{ old('avatar_seed', $user->avatar_seed ?: $user->name) }}',
                            previewUrl() {
                                return '{{ route('avatar.preview') }}?style=' + encodeURIComponent(this.selectedStyle) + '&seed=' + encodeURIComponent(this.avatarSeed || 'user');
                            },
                            randomizeSeed() {
                                const words = ['Pro', 'Master', 'Admin', 'Kreatif', 'Bintang', 'Pendidik', 'Cerdas', 'Hebat', 'Inovator', 'Juara'];
                                const randNum = Math.floor(Math.random() * 9000) + 1000;
                                this.avatarSeed = words[Math.floor(Math.random() * words.length)] + '-' + randNum;
                            }
                        }">
                            <x-form-section
                                title="Avatar Profil (DiceBear)"
                                description="Pilih gaya ilustrasi dan tentukan benih (seed) karakter avatar akun Anda."
                                icon="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            >
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:flex-row sm:items-center">
                                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-sm ring-2 ring-blue-500/20">
                                            <img :src="previewUrl()" alt="Preview Avatar" class="h-full w-full object-cover">
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h4 class="text-sm font-bold text-slate-900">Pratinjau Avatar Langsung</h4>
                                            <p class="mt-0.5 text-xs text-slate-500">Avatar digenerate otomatis menggunakan pustaka resmi DiceBear. Klik tombol acak atau ubah teks kata kunci untuk ekspresi unik.</p>
                                            <div class="mt-2.5 flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    @click="randomizeSeed()"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 shadow-2xs hover:bg-slate-50 hover:text-blue-600 transition"
                                                >
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                    </svg>
                                                    <span>Acak Karakter</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    @click="avatarSeed = '{{ $user->name }}'"
                                                    class="inline-flex items-center rounded-lg border border-transparent px-2.5 py-1.5 text-xs font-medium text-slate-500 hover:text-slate-800 transition"
                                                >
                                                    Reset ke Nama Saya
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-[13px] font-semibold text-slate-700">Pilih Gaya Karakter Avatar</label>
                                        <input type="hidden" name="avatar_style" :value="selectedStyle">
                                        <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 md:grid-cols-5">
                                            @foreach ($avatarStyles as $sKey => $sMeta)
                                                <button
                                                    type="button"
                                                    @click="selectedStyle = '{{ $sKey }}'"
                                                    class="flex flex-col items-center gap-2 rounded-xl border p-2.5 text-center transition-all"
                                                    :class="selectedStyle === '{{ $sKey }}' ? 'border-blue-500 bg-blue-50/70 shadow-sm ring-2 ring-blue-500/20 text-blue-900 font-bold' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                                                >
                                                    <span class="flex h-11 w-11 items-center justify-center overflow-hidden rounded-xl bg-slate-100 ring-1 ring-slate-200">
                                                        <img src="{{ \App\Services\AvatarService::getAvatarUrl($sKey, 'preview') }}" alt="{{ $sMeta['label'] }}" loading="lazy" class="h-full w-full object-cover">
                                                    </span>
                                                    <span class="text-[11px] leading-tight font-medium" :class="selectedStyle === '{{ $sKey }}' ? 'font-bold text-blue-800' : ''">{{ strtok($sMeta['label'], ' ') }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="space-y-1">
                                        <label for="avatar_seed_input" class="block text-[13px] font-semibold text-slate-700">Kata Kunci Benih (Seed Avatar)</label>
                                        <input
                                            type="text"
                                            id="avatar_seed_input"
                                            name="avatar_seed"
                                            x-model.debounce.400ms="avatarSeed"
                                            class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-[13px] text-slate-900 shadow-sm transition placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            placeholder="Masukkan kata apa saja untuk menghasilkan ekspresi avatar"
                                        >
                                        <p class="text-[11px] text-slate-500">Kombinasi gaya avatar dan teks benih ini menentukan rupa profil Anda secara konsisten.</p>
                                    </div>
                                </div>
                            </x-form-section>
                        </div>

                        <x-form-section
                            title="Ganti Kata Sandi"
                            description="Kosongkan seluruh kolom bila tidak ingin mengubah kata sandi."
                            icon="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                        >
                            <div class="space-y-4">
                                <div class="rounded-lg border border-amber-200 bg-amber-50/70 p-3">
                                    <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-amber-800">
                                        <svg class="mt-0.5 h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5 19h14a2 2 0 001.84-2.75L13.74 4a2 2 0 00-3.5 0L3.16 16.25A2 2 0 005 19z"/>
                                        </svg>
                                        <span>Kata sandi baru minimal 8 karakter. Setelah disimpan, gunakan kata sandi baru pada login berikutnya.</span>
                                    </p>
                                </div>

                                <x-input
                                    label="Kata Sandi Saat Ini"
                                    type="password"
                                    name="current_password"
                                    placeholder="Masukkan kata sandi lama"
                                    autocomplete="current-password"
                                />

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <x-input
                                        label="Kata Sandi Baru"
                                        type="password"
                                        name="password"
                                        placeholder="Minimal 8 karakter"
                                        autocomplete="new-password"
                                    />
                                    <x-input
                                        label="Ulangi Kata Sandi Baru"
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Ketik ulang kata sandi baru"
                                        autocomplete="new-password"
                                    />
                                </div>
                            </div>
                        </x-form-section>

                        <div class="flex flex-col-reverse gap-2 rounded-xl border border-slate-200/90 bg-white px-5 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-[11px] text-slate-500">Perubahan tercatat pada audit log sistem.</p>
                            <x-button type="submit" variant="primary">Simpan Perubahan</x-button>
                        </div>
                    </form>
                </div>

                {{-- Aside identitas akun --}}
                <aside class="space-y-5">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-[3px] bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5 text-center">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl bg-white shadow-md ring-4 ring-slate-100">
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="h-full w-full object-cover">
                            </div>
                            <p class="mt-3 text-[14px] font-bold leading-snug text-slate-900">{{ $user->name }}</p>
                            <p class="mt-0.5 text-[11.5px] text-slate-500">{{ $user->email }}</p>

                            <div class="mt-3 flex justify-center">
                                @if ($user->isSuperAdmin())
                                    <x-badge variant="primary" size="sm" dot>Super Admin</x-badge>
                                @else
                                    <x-badge variant="secondary" size="sm" dot>Admin</x-badge>
                                @endif
                            </div>
                        </div>

                        <dl class="divide-y divide-slate-100 border-t border-slate-100 text-[11.5px]">
                            <div class="flex items-center justify-between gap-3 px-5 py-2.5">
                                <dt class="text-slate-500">NIP</dt>
                                <dd class="tabular truncate font-mono font-semibold text-slate-800">{{ $user->nip ?: '-' }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-3 px-5 py-2.5">
                                <dt class="text-slate-500">Status Akun</dt>
                                <dd>
                                    @if ($user->is_active ?? true)
                                        <x-badge variant="success" size="sm" dot>Aktif</x-badge>
                                    @else
                                        <x-badge variant="danger" size="sm" dot>Nonaktif</x-badge>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </section>

                    <section class="rounded-xl border border-slate-200/90 bg-slate-50/70 p-4">
                        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-slate-600">
                            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Perubahan profil dicatat pada audit log sistem beserta waktu dan aktornya.</span>
                        </p>
                    </section>
                </aside>
            </div>
        @endif

        {{-- ============ TAB: IDENTITAS SEKOLAH ============ --}}
        @if ($tab === 'sistem' && auth()->user()->isSuperAdmin())
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_300px] lg:gap-6">
                <div class="space-y-5">
                    <x-form-section
                        title="Identitas Lembaga"
                        description="Dipakai sebagai kop surat resmi pada laporan PDF dan cetak."
                        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                    >
                        <form method="POST" action="{{ route('admin.pengaturan.system') }}" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <x-input
                                label="Nama Lembaga / Sekolah"
                                name="school_name"
                                :value="old('school_name', $systemSettings['school_name'])"
                                required
                            />

                            <div>
                                <label for="school_address" class="mb-1.5 block text-[13px] font-semibold text-slate-700">Alamat Resmi Sekolah</label>
                                <textarea
                                    id="school_address"
                                    name="school_address"
                                    rows="2"
                                    required
                                    class="block w-full rounded-lg border bg-white px-3 py-2.5 text-[13px] text-slate-900 shadow-sm transition-colors placeholder:text-slate-500 hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/30 @error('school_address') border-rose-400 @else border-slate-300 @enderror"
                                >{{ old('school_address', $systemSettings['school_address']) }}</textarea>
                                @error('school_address')
                                    <p class="mt-1 text-[11.5px] font-medium text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input
                                    label="Nomor Telepon Sekolah"
                                    name="school_phone"
                                    :value="old('school_phone', $systemSettings['school_phone'])"
                                    placeholder="(0233) 000000"
                                />
                                <x-input
                                    label="Email Resmi Sekolah"
                                    type="email"
                                    name="school_email"
                                    :value="old('school_email', $systemSettings['school_email'])"
                                    placeholder="info@sekolah.sch.id"
                                />
                            </div>

                            <div class="border-t border-slate-100 pt-4">
                                <h4 class="text-[12px] font-bold uppercase tracking-[0.08em] text-slate-500">Pejabat Pengesah</h4>
                                <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <x-input
                                        label="Nama Kepala Sekolah"
                                        name="headmaster_name"
                                        :value="old('headmaster_name', $systemSettings['headmaster_name'])"
                                        required
                                    />
                                    <x-input
                                        label="NIP Kepala Sekolah"
                                        name="headmaster_nip"
                                        :value="old('headmaster_nip', $systemSettings['headmaster_nip'])"
                                        required
                                    />
                                </div>
                            </div>

                            <div class="border-t border-slate-100 pt-4">
                                <x-input
                                    label="Penyelenggara Kegiatan Default"
                                    name="default_organizer"
                                    :value="old('default_organizer', $systemSettings['default_organizer'])"
                                    required
                                />
                                <p class="mt-1.5 text-[11px] text-slate-500">Nilai ini otomatis terisi saat membuat pertemuan baru.</p>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-100 pt-4">
                                <x-button type="submit" variant="primary">Simpan Identitas</x-button>
                            </div>
                        </form>
                    </x-form-section>
                </div>

                <aside class="space-y-5">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm">
                        <div class="flex items-center gap-3">
                            <img
                                src="{{ asset('images/logo-smkn1majalengka-trimmed.webp') }}"
                                alt="Lambang SMK Negeri 1 Majalengka"
                                width="1196"
                                height="1127"
                                class="h-14 w-14 object-contain"
                            >
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold uppercase tracking-[0.1em] text-slate-500">Lembaga Aktif</p>
                                <p class="mt-0.5 text-[12.5px] font-bold leading-snug text-slate-900">SMKN 1 Majalengka</p>
                            </div>
                        </div>
                        <p class="mt-3 border-t border-slate-100 pt-3 text-[11px] leading-relaxed text-slate-500">
                            Lambang resmi ini otomatis tercetak pada kop laporan. Jangan mengganti dengan logo lain.
                        </p>
                    </section>

                    <section class="rounded-xl border border-blue-200 bg-blue-50/60 p-4">
                        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-blue-900">
                            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            <span>Hanya Super Admin yang dapat mengubah identitas lembaga.</span>
                        </p>
                    </section>
                </aside>
            </div>
        @endif

        {{-- ============ TAB: INFORMASI SERVER ============ --}}
        @if ($tab === 'info')
            <div class="grid grid-cols-1 gap-5 lg:grid-cols-[minmax(0,1fr)_300px] lg:gap-6">
                <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                    <div class="flex items-start gap-3 border-b border-slate-100 px-5 py-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.9" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-[14px] font-bold leading-snug text-slate-900">Informasi Lingkungan Server</h3>
                            <p class="mt-0.5 text-[11.5px] text-slate-500">Parameter runtime yang aktif untuk instalasi DIHADIR ini.</p>
                        </div>
                    </div>

                    <dl class="divide-y divide-slate-100">
                        @foreach ([
                            ['Versi PHP', $serverInfo['php_version'], 'mono'],
                            ['Versi Laravel', $serverInfo['laravel_version'], 'mono'],
                            ['Environment', strtoupper($serverInfo['app_env']), 'badge'],
                            ['Status Debug', $serverInfo['app_debug'], 'text'],
                            ['Zona Waktu', $serverInfo['timezone'], 'mono'],
                            ['Koneksi Database', $serverInfo['db_connection'], 'mono'],
                            ['Waktu Server', $serverInfo['current_time'], 'accent'],
                        ] as $row)
                            <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-3">
                                <dt class="text-[12px] text-slate-500">{{ $row[0] }}</dt>
                                <dd class="text-[12px]">
                                    @if ($row[2] === 'badge')
                                        <x-badge :variant="$serverInfo['app_env'] === 'production' ? 'success' : 'warning'" size="sm" dot>{{ $row[1] }}</x-badge>
                                    @elseif ($row[2] === 'accent')
                                        <span class="tabular font-mono font-bold text-blue-700">{{ $row[1] }}</span>
                                    @elseif ($row[2] === 'mono')
                                        <span class="tabular font-mono font-semibold text-slate-800">{{ $row[1] }}</span>
                                    @else
                                        <span class="font-semibold text-slate-800">{{ $row[1] }}</span>
                                    @endif
                                </dd>
                            </div>
                        @endforeach
                    </dl>
                </section>

                <aside class="space-y-5">
                    <section class="overflow-hidden rounded-xl border border-slate-700 bg-slate-900 p-5 shadow-sm">
                        <div class="mb-3 flex items-center justify-between gap-2">
                            <h3 class="text-[12px] font-bold text-slate-200">Ringkasan Operasional</h3>
                            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-400/30 bg-emerald-500/15 px-2 py-0.5 text-[10px] font-bold text-emerald-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span>
                                Normal
                            </span>
                        </div>
                        <dl class="space-y-2 text-[11px]">
                            @foreach ([
                                ['Zona Waktu', 'Asia/Jakarta (WIB)', 'text-white'],
                                ['Penyimpanan TTD', 'Private Disk', 'text-blue-300'],
                                ['Anti-Duplikasi', 'NIP + Meeting Lock', 'text-emerald-300'],
                                ['Clean URL', 'Aktif', 'text-emerald-300'],
                            ] as $row)
                                <div class="flex items-center justify-between gap-3 border-b border-white/[0.07] pb-2 last:border-0 last:pb-0">
                                    <dt class="text-slate-500">{{ $row[0] }}</dt>
                                    <dd class="font-mono font-semibold {{ $row[2] }}">{{ $row[1] }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </section>

                    <section class="rounded-xl border border-slate-200/90 bg-slate-50/70 p-4">
                        <p class="flex items-start gap-2 text-[11.5px] leading-relaxed text-slate-600">
                            <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Informasi ini bersifat baca-saja. Untuk mengubahnya, sesuaikan berkas <code class="rounded bg-white px-1 py-0.5 font-mono text-[10.5px] text-slate-700 ring-1 ring-inset ring-slate-200">.env</code> pada server.</span>
                        </p>
                    </section>
                </aside>
            </div>
        @endif
    </div>
</x-layouts.admin>
