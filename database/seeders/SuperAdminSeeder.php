<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * ZERO DEMO DATA: hanya menginisialisasi satu akun Super Admin resmi.
     * Kredensial dibaca dari konfigurasi environment; tidak ada kata sandi
     * bawaan yang tertulis di source code.
     */
    public function run(): void
    {
        $name = trim((string) config('app.superadmin.name')) ?: 'Super Administrator';
        $email = trim((string) config('app.superadmin.email'));
        $password = (string) config('app.superadmin.password');

        if ($email === '') {
            $this->command?->warn(
                'SUPERADMIN_EMAIL belum diatur. Seeder dilewati. '
                . 'Isi SUPERADMIN_NAME, SUPERADMIN_EMAIL, dan SUPERADMIN_PASSWORD pada .env lalu jalankan ulang seeder.'
            );

            return;
        }

        if (User::where('email', $email)->exists()) {
            $this->command?->info("Akun Super Admin dengan email {$email} sudah tersedia. Tidak ada data yang diubah.");

            return;
        }

        $generatedPassword = false;

        if ($password === '') {
            $password = Str::password(24, symbols: false);
            $generatedPassword = true;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->command?->info('Akun Super Admin awal berhasil dibuat.');
        $this->command?->line("Email    : {$email}");

        if ($generatedPassword) {
            $this->command?->warn("Kata sandi dibuat otomatis: {$password}");
            $this->command?->warn('Catat kata sandi ini sekarang; kata sandi tidak ditampilkan lagi. Ubah segera setelah login pertama.');
        }
    }
}
