# FASE 01 — FOUNDATION, DATABASE & AUTENTIKASI ADMIN

Dokumen ini memuat spesifikasi teknis untuk tahap inisialisasi lingkungan kerja, perancangan basis data dasar, autentikasi multi-role, dan kerangka tata letak (layout) visual DIHADIR.

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - Dilarang membuat akun admin contoh, data tester, atau seeder demo.
> - Seeder HANYA membuat 1 akun Super Admin awal resmi menggunakan data kredensial terkonfigurasi (dari `.env` seperti `SUPERADMIN_EMAIL` & `SUPERADMIN_PASSWORD`).
> - Tidak ada data dummy yang di-insert ke dalam database.

1. Menyiapkan konfigurasi dasar aplikasi Laravel 12 (timezone `Asia/Jakarta`, locale `id`).
2. Menyiapkan schema database dasar: tabel `users`, role enum/pivot, sesi, dan password reset.
3. Mengimplementasikan sistem autentikasi untuk pengelola (Super Admin & Admin Acara).
4. Menyiapkan kerangka layout Blade dengan design system *Slate & Blue (Minimalist Corporate)*.
5. Menyiapkan Seeder khusus inisialisasi akun Super Admin resmi pertama kali.

---

## 2. Rincian Teknis & Arsitektur

### 2.1 Konfigurasi Dasar Aplikasi & Clean URLs
- **Routing & Clean URLs:** Seluruh URL aplikasi wajib Pretty/Clean URLs **tanpa ekstensi `.php` atau `index.php`** (misalnya `/admin/login`, `/admin/dashboard`).
- **Timezone:** Pastikan timezone aplikasi diatur ke `Asia/Jakarta` pada konfigurasi framework agar seluruh pencatatan waktu kehadiran akurat.
- **Locale:** Menggunakan Bahasa Indonesia (`id`) untuk format tanggal, jam, dan pesan validasi.
- **Aset Frontend & Logo Resmi:**
  - Favicon: `public/images/logo-dihadir-icon.png`
  - Logo Utama Aplikasi: `public/images/logo-dihadir-full.png`
  - Lambang Resmi Sekolah: `public/images/logo-smkn1majalengka.png`
  - Tailwind CSS v4, Inter font (`@fontsource-variable/inter`), dan Alpine.js terhubung di `resources/js/app.js` dan `resources/css/app.css`.

### 2.2 Skema Database (Migration)

#### A. Tabel `users`
Tabel ini digunakan untuk akun admin pengelola sistem:
- `id` (bigIncrements / uuid)
- `name` (string)
- `email` (string, unique)
- `username` / `nip` (string, nullable, unique)
- `password` (string, hashed)
- `role` (enum: `super_admin`, `admin` - default `admin`)
- `is_active` (boolean, default true)
- `remember_token`
- `created_at` & `updated_at`

### 2.3 Role & Authorization

Terapkan otorisasi menggunakan Laravel Gates/Policies:
- **Super Admin:** Memiliki hak penuh untuk mengelola pengguna, melihat semua acara/pertemuan, dan konfigurasi sistem.
- **Admin:** Memiliki akses operasional ke acara/pertemuan yang ditugaskan kepadanya.
- Middleware:
  - `auth`: Memastikan user telah login.
  - `role:super_admin`: Membatasi area super admin.
  - `role:admin,super_admin`: Area panel admin.

### 2.4 Kerangka Layout Blade (*Slate & Blue*)

Sesuai panduan di [AGENTS.md](file:///D:/DIHADIR/AGENTS.md#L427-L460):

1. **`resources/views/layouts/admin.blade.php`**
   - **Sidebar:** Latar warna Slate (`#334155`), teks putih/slate-200, logo & branding DIHADIR SMKN 1 Majalengka di bagian atas, dan tombol logout di bagian bawah.
   - **Topbar:** Latar putih (`#FFFFFF`) dengan border halus `#CBD5E1`, breadcrumb, dan info user login.
   - **Content Area:** Latar belakang abu-abu terang `#F8FAFC`, tipografi font Inter, container terstruktur.
2. **`resources/views/layouts/guest.blade.php`**
   - Layout bersih untuk form publik dan login absensi peserta, mobile-first, tanpa sidebar.

---

## 3. Langkah Implementasi (Task Checklist)

- [ ] Konfigurasi `.env` (DB connection, APP_TIMEZONE=Asia/Jakarta, APP_LOCALE=id).
- [ ] Buat migration untuk penyesuaian tabel `users` (penambahan field `role`, `is_active`, `nip`).
- [ ] Buat model `User` dengan helper method: `isSuperAdmin()`, `isAdmin()`.
- [ ] Buat controller autentikasi admin: `App\Http\Controllers\Auth\AdminLoginController`:
  - `showLoginForm()`
  - `login(AdminLoginRequest $request)`
  - `logout()`
- [ ] Buat Blade view login admin: `resources/views/auth/admin-login.blade.php` dengan gaya *Minimalist Corporate*.
- [ ] Buat Seeder: `Database\Seeders\SuperAdminSeeder` (HANYA 1 akun Super Admin resmi pertama kali dari konfigurasi `.env`, tanpa akun demo/contoh).
- [ ] Buat Base Components Blade:
  - `<x-button>`
  - `<x-input>`
  - `<x-card>`
  - `<x-badge>`
  - `<x-alert>`

---

## 4. Kriteria Pengujian (Definition of Done)

1. Jalankan `php artisan migrate --seed` berhasil tanpa error.
2. Login Admin berhasil menggunakan email/password yang valid dan dialihkan ke `/admin/dashboard`.
3. Akses rute yang dilindungi tanpa login dialihkan kembali ke form login dengan pesan flash.
4. Tampilan login dan layout admin sesuai palet warna Slate & Blue dan responsif di mobile/desktop.
