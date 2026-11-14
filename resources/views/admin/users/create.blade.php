<x-layouts.admin title="Tambah Akun Admin Baru" heading="Tambah Admin">
    <div class="w-full">
        <x-page-header
            title="Tambah Akun Admin Baru"
            description="Daftarkan akun administrator baru untuk membantu mengelola acara dan presensi."
            eyebrow="Manajemen Sistem"
            :back-route="route('admin.users.index')"
            back-label="Kelola Admin"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                    <x-form-section title="Identitas Admin" description="Informasi dasar dan kredensial login akun." :step="1">
                        <div class="space-y-4">
                            <x-input
                                label="Nama Lengkap"
                                name="name"
                                id="name"
                                type="text"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Ahmad Fauzi, S.Kom."
                                :required="true"
                                :error="$errors->first('name')"
                                autofocus
                            />

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <x-input
                                    label="Alamat Email (Username Login)"
                                    name="email"
                                    id="email"
                                    type="email"
                                    value="{{ old('email') }}"
                                    placeholder="nama@smkn1majalengka.sch.id"
                                    :required="true"
                                    :error="$errors->first('email')"
                                />

                                <x-input
                                    label="NIP (Nomor Induk Pegawai)"
                                    name="nip"
                                    id="nip"
                                    type="text"
                                    value="{{ old('nip') }}"
                                    placeholder="Contoh: 198501012015011001"
                                    :error="$errors->first('nip')"
                                    inputmode="numeric"
                                    helper="Opsional, dapat digunakan sebagai alternatif login."
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-custom-select
                                        label="Peran / Hak Akses"
                                        name="role"
                                        id="role"
                                        :value="old('role', 'admin')"
                                        :required="true"
                                        :error="$errors->first('role')"
                                        :options="[
                                            'admin' => 'Admin Pengelola (Hanya Acara Ditugaskan)',
                                            'super_admin' => 'Super Admin (Akses Penuh Seluruh Sistem)',
                                        ]"
                                    />
                                </div>

                                <div class="flex items-center pt-5">
                                    <label class="flex cursor-pointer select-none items-center gap-2.5 rounded-lg border border-slate-200 bg-slate-50/70 p-2.5 text-xs">
                                        {{-- Nilai 0 wajib dikirim agar menonaktifkan akun benar-benar tersimpan. --}}
                                        <input type="hidden" name="is_active" value="0">
                                        <input
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', '1') ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="font-semibold text-slate-800">Status Akun Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </x-form-section>

                    <x-form-section title="Keamanan Kata Sandi" description="Tentukan kata sandi akun untuk akses panel admin." :step="2">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Kata Sandi Akun"
                                name="password"
                                id="password"
                                type="password"
                                :required="true"
                                placeholder="Minimal 8 karakter"
                                :error="$errors->first('password')"
                                autocomplete="new-password"
                            />

                            <x-input
                                label="Konfirmasi Kata Sandi"
                                name="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                :required="true"
                                placeholder="Ulangi kata sandi"
                                autocomplete="new-password"
                            />
                        </div>
                    </x-form-section>

                    <div class="sticky bottom-0 -mx-1 flex items-center justify-end gap-2.5 rounded-xl border border-slate-200/90 bg-white/95 px-4 py-3 shadow-sm backdrop-blur-sm">
                        <x-button :href="route('admin.users.index')" variant="outline">Batal</x-button>
                        <x-button type="submit" variant="primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Simpan Akun Admin</span>
                        </x-button>
                    </div>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Perbedaan Peran Akun</h3>
                            <div class="mt-3.5 space-y-3 text-xs leading-relaxed">
                                <div class="rounded-lg bg-slate-50 p-3 border border-slate-200">
                                    <p class="font-bold text-blue-700">Super Admin</p>
                                    <p class="mt-0.5 text-[11px] text-slate-600">Akses lintas semua pertemuan, mengelola akun admin lain, pengaturan identitas sekolah, dan melihat jejak audit log sistem.</p>
                                </div>
                                <div class="rounded-lg bg-slate-50 p-3 border border-slate-200">
                                    <p class="font-bold text-slate-800">Admin Pengelola</p>
                                    <p class="mt-0.5 text-[11px] text-slate-600">Hanya dapat mengelola, memantau rekap, dan mengunduh laporan pada acara kegiatan yang ditugaskan kepadanya.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
