# FASE 06 — PENGATURAN, KEAMANAN, PENGUJIAN & KESIAPAN PRODUKSI

Dokumen ini memuat spesifikasi teknis untuk modul **Pengaturan** (profil admin & identitas sistem sekolah), fitur **Logout**, audit keamanan (*Security Hardening*), pengujian otomatis (*Automated Testing*), serta prosedur rilis ke lingkungan produksi (*Deployment & Production Readiness*).

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - DILARANG KERAS menyertakan data demo, akun uji coba publik, atau data pertemuan fiktif ke lingkungan produksi.
> - Seeder produksi (`ProductionSeeder`) HANYA menginisialisasi 1 akun Super Admin awal resmi (kredensial dari environment) dan data referensi statis (daftar Kabupaten/Kota).
> - Seluruh pengujian otomatis (automated testing) menggunakan database test terpisah (`sqlite :memory:` atau DB test) dan di-rollback otomatis sehingga tidak mengotori database produksi.

1. Membangun modul **Pengaturan** (`/admin/settings`) untuk pembaruan profil admin dan konfigurasi institusi sekolah.
2. Memastikan alur **Logout** berjalan aman dengan invalidasi sesi dan regenerasi token CSRF.
3. Menerapkan *security hardening* (rate limiting, private storage storage untuk tanda tangan, proteksi CSRF, dan sanitasi XSS).
4. Menyusun skenario pengujian otomatis (Feature & Unit Tests) berbasis PHPUnit.
5. Menyiapkan panduan rilis produksi, optimasi caching Laravel, dan prosedur backup/restore database.

---

## 2. Rincian Teknis & Arsitektur

### 2.1 Modul Pengaturan (`/admin/settings`)

Menu Sidebar: **Pengaturan**

Halaman pengaturan dibagi menjadi dua bagian:
1. **Pengaturan Akun (Profil Saya):**
   - Mengubah nama lengkap dan email admin.
   - Mengubah kata sandi (wajib memasukkan kata sandi saat ini / *current password*).
2. **Pengaturan Sistem Sekolah (Khusus Super Admin):**
   - Nama Lembaga: SMKN 1 Majalengka.
   - Penyelenggara Default & Alamat Sekolah.
   - Pejabat Penandatangan Laporan (Nama Kepala Sekolah & NIP untuk footer laporan resmi).
   - Upload & kelola logo sekolah beresolusi tinggi.
   - Pengaturan default timezone (`Asia/Jakarta`).

### 2.2 Logout & Manajemen Sesi

Menu Sidebar: **Logout**
- Permintaan logout wajib menggunakan metode `HTTP POST` berpelindung `@csrf` (mencegah serangan CSRF via tag `<img src="/logout">`).
- Controller melakukan `Auth::guard('web')->logout()`, `request()->session()->invalidate()`, dan `request()->session()->regenerateToken()`.
- Pengguna dialihkan kembali ke halaman login admin dengan notifikasi aman.

---

### 2.3 Keamanan Aplikasi (*Security Hardening*)

Sesuai aturan di [AGENTS.md](file:///D:/DIHADIR/AGENTS.md#L333-L376):
- **Rate Limiting:**
  - Login Admin: Maksimal 5 percobaan gagal per menit (`throttle:admin-login`).
  - Form Login Absensi Peserta: Maksimal 10 percobaan per menit per IP (`throttle:participant-login`).
  - Submit Absensi: Maksimal 10 submit per menit per IP (`throttle:attendance-submit`).
- **Penyimpanan Tanda Tangan Privat:**
  - File tanda tangan digital (`.png`) disimpan di disk `private` (misal `storage/app/signatures/{meeting_id}/{filename}.png`).
  - Tidak dihubungkan ke `public/storage` symlink agar tidak dapat diakses bebas atau di-index oleh mesin pencari.
  - Akses gambar tanda tangan hanya melalui controller yang memeriksa permission admin.
- **Escape Output & Sanitasi:**
  - Seluruh output teks pada view Blade menggunakan `{{ $var }}` bawaan untuk mencegah XSS.

---

### 2.4 Pengujian Otomatis (*Automated Testing*)

Jalankan pengujian menggunakan `php artisan test`:

1. **`Tests\Feature\AdminAuthTest`:**
   - Memastikan admin dapat login dengan data valid dan ditolak dengan data salah.
   - Memastikan Super Admin dapat mengakses menu manajemen pengguna.
2. **`Tests\Feature\MeetingManagementTest`:**
   - Memastikan admin hanya dapat mengelola kegiatan pertemuan yang ditugaskan kepadanya.
   - Memastikan token publik ter-generate secara acak dan unik.
3. **`Tests\Feature\ParticipantLoginTest`:**
   - Memastikan form login absensi menolak submit jika verifikasi geser belum dilakukan.
   - Memastikan peserta dapat login dengan NIP dan password terdaftar.
4. **`Tests\Feature\AttendanceSubmissionTest`:**
   - Memastikan form publik menyimpan data peserta dan tanda tangan digital dengan waktu server yang benar.
   - Memastikan pencegahan pengisian ganda (duplicate submit) dengan NIP yang sama pada pertemuan yang sama berhasil digagalkan.
5. **`Tests\Feature\ReportExportTest`:**
   - Memastikan ekspor PDF dan Excel hanya dapat diunduh oleh admin yang berwenang.

---

### 2.5 Checklist Kesiapan Produksi & Web Server (Clean URLs)

Sebelum aplikasi digunakan secara resmi di SMKN 1 Majalengka:
- [ ] **Web Server URL Rewriting (Clean URLs Tanpa `.php`):**
  - Pada **Nginx**: Pastikan blok lokasi menyertakan direktif:
    ```nginx
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    ```
  - Pada **Apache**: Pastikan modul `mod_rewrite` aktif dan file `public/.htaccess` terbaca sempurna sehingga tidak ada URL berakhiran `.php` atau `index.php`.
- [ ] **Aset Logo Resmi:**
  - Verifikasi ketiga berkas logo resmi telah ada di direktori publik:
    - `public/images/logo-dihadir-icon.png`
    - `public/images/logo-dihadir-full.png`
    - `public/images/logo-smkn1majalengka.png`
  - Hak akses berkas (file permission) diatur agar terbaca oleh web server (`chmod 644`).
- [ ] Set `.env`: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://dihadir.smkn1majalengka.sch.id` (atau domain sekolah terkait).
- [ ] Konfigurasi database produksi (PostgreSQL).
- [ ] Jalankan migrasi dan seeder awal:
  ```bash
  php artisan migrate --force
  php artisan db:seed --class=ProductionSeeder --force
  ```
- [ ] Optimasi performa:
  ```bash
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  npm run build
  ```
- [ ] Atur scheduled job / backup otomatis database (misalnya `spatie/laravel-backup` harian ke storage aman).
- [ ] Buat dokumentasi panduan singkat (Buku Petunjuk Operasional) untuk Panitia Pertemuan dan Orang Tua Siswa.

---

## 3. Kriteria Pengujian (Definition of Done)

1. Halaman Pengaturan memungkinkan admin memperbarui identitas dan password dengan validasi ketat.
2. Form logout tidak rentan terhadap serangan CSRF dan mengosongkan sesi sepenuhnya.
3. Seluruh unit dan feature tests lulus (`PASS`) tanpa ada kegagalan regresi.
4. Aplikasi berjalan stabil pada environment produksi dengan `APP_DEBUG=false`.
