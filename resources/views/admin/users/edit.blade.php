<x-layouts.admin title="Ubah Akun Admin" heading="Ubah Admin">
    <div class="w-full">
        <x-page-header
            title="Ubah Akun Admin: {{ $user->name }}"
            description="Perbarui informasi profil, hak akses peran, atau atur ulang kata sandi akun."
            eyebrow="Manajemen Sistem"
            :back-route="route('admin.users.index')"
            back-label="Kelola Admin"
            class="mb-5"
        />

        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_336px] xl:items-start xl:gap-6">
                <div class="min-w-0 space-y-4 sm:space-y-5">
                    <x-form-section title="Identitas Admin" description="Informasi dasar dan status peran akun." :step="1">
                        <div class="space-y-4">
                            <x-input
                                label="Nama Lengkap"
                                name="name"
                                id="name"
                                type="text"
                                value="{{ old('name', $user->name) }}"
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
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="nama@smkn1majalengka.sch.id"
                                    :required="true"
                                    :error="$errors->first('email')"
                                />

                                <x-input
                                    label="NIP (Nomor Induk Pegawai)"
                                    name="nip"
                                    id="nip"
                                    type="text"
                                    value="{{ old('nip', $user->nip) }}"
                                    placeholder="Contoh: 198501012015011001"
                                    :error="$errors->first('nip')"
                                    inputmode="numeric"
                                />
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <x-custom-select
                                        label="Peran / Hak Akses"
                                        name="role"
                                        id="role"
                                        :value="old('role', $user->role)"
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
                                            id="user_is_active"
                                            aria-label="Status Pengelola Aktif"
                                            type="checkbox"
                                            name="is_active"
                                            value="1"
                                            {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                            class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                        >
                                        <span class="font-semibold text-slate-800">Status Akun Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </x-form-section>

                    <x-form-section title="Ubah Kata Sandi (Opsional)" description="Biarkan kosong jika tidak bermaksud mengubah kata sandi saat ini." :step="2">
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <x-input
                                label="Kata Sandi Baru"
                                name="password"
                                id="password"
                                type="password"
                                placeholder="Kosongkan jika tidak diubah"
                                :error="$errors->first('password')"
                                helper="Minimal 8 karakter bila diisi."
                                autocomplete="new-password"
                            />

                            <x-input
                                label="Konfirmasi Kata Sandi Baru"
                                name="password_confirmation"
                                id="password_confirmation"
                                type="password"
                                placeholder="Ulangi kata sandi baru"
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
                            <span>Perbarui Akun Admin</span>
                        </x-button>
                    </div>
                </div>

                <aside class="space-y-4 xl:sticky xl:top-24">
                    <section class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-sm">
                        <div class="h-1 bg-blue-600" aria-hidden="true"></div>
                        <div class="p-5">
                            <h3 class="text-[13px] font-bold text-slate-900">Perlindungan Akun</h3>
                            <p class="mt-2 text-xs leading-relaxed text-slate-600">
                                Sistem melindungi integritas sistem dengan mencegah penurunan peran atau penonaktifan pada akun Super Admin aktif terakhir.
                            </p>
                        </div>
                    </section>
                </aside>
            </div>
        </form>
    </div>
</x-layouts.admin>
