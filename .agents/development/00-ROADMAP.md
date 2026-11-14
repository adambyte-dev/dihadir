# ROADMAP & ARSITEKTUR IMPLEMENTASI DIHADIR

Dokumen ini memetakan tahapan implementasi teknis untuk sistem **DIHADIR (Digital Attendance Management System)** di lingkungan SMKN 1 Majalengka. Seluruh fase mengacu pada pedoman arsitektur, domain, aturan bisnis, dan antarmuka di [AGENTS.md](file:///D:/DIHADIR/AGENTS.md).

---

## 1. Ringkasan Eksekutif & Stack Teknologi

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI (PRODUCTION-READY)**
> - **DILARANG KERAS membuat data dummy, mock data, atau generator data fiktif** (seperti Faker untuk database seeder, pertemuan contoh, peserta palsu, atau absensi tiruan) di kode maupun database.
> - **Database dalam kondisi bersih (clean state).** Seeder yang diizinkan HANYALAH pembuatan 1 akun Super Admin awal resmi (menggunakan kredensial aman dari `.env`) dan data referensi statis sistem (seperti daftar baku Kabupaten/Kota).
> - **Terapkan Empty State yang elegan.** Seluruh halaman (Dashboard, Tabel Pertemuan, Tabel Peserta, Rekap Kehadiran, Laporan) WAJIB mengimplementasikan komponen *Empty State* yang bersih dan profesional saat data belum ada, BUKAN diisi data palsu.
> - **Semua metrik statistik dihitung murni dari data riil.** Nilai Total Peserta, Sudah Hadir, Belum Hadir, dan Total Pertemuan berasal dari query database nyata (menampilkan 0 jika sistem baru).
> - **Seluruh fungsionalitas harus langsung berfungsi nyata** untuk operasional sekolah SMKN 1 Majalengka tanpa ada *mock logic* atau *placeholder*.

- **Framework:** Laravel 12 (PHP 8.2+)
- **Routing:** Clean / Pretty URLs (**DILARANG** menampilkan `.php` atau `index.php` pada URL browser)
- **Frontend & View:** Laravel Blade + Alpine.js + Tailwind CSS v4
- **Tipografi & Ikon:** Inter (`@fontsource-variable/inter`), Lucide Icons
- **Database:** Relasional (PostgreSQL / SQLite untuk dev)
- **Aset Logo Resmi (Tersimpan di `public/images/`):**
  - `public/images/logo-dihadir-icon.png` (Favicon & Icon mini)
  - `public/images/logo-dihadir-full.png` (Logo horizontal aplikasi untuk sidebar, topbar & login)
  - `public/images/logo-smkn1majalengka.png` (Lambang resmi SMKN 1 Majalengka untuk KOP laporan & identitas instansi)
- **Design System:** *Slate & Blue — Minimalist Corporate*
  - Primary: `#334155` (Slate-700)
  - Accent: `#3B82F6` (Blue-500)
  - Surface: `#FFFFFF`
  - Background: `#F8FAFC` (Slate-50)
  - Text: `#0F172A` (Slate-900)

---

## 2. Struktur Pembagian Fase Implementasi

Direktori `development/` dibagi menjadi modul-modul terpisah agar pengembangan dapat dilakukan secara bertahap dan terukur:

| File Fase | Fokus & Modul | Output Utama | Status |
|---|---|---|---|
| [PHASE-01-FOUNDATION-AND-AUTH.md](file:///D:/DIHADIR/development/PHASE-01-FOUNDATION-AND-AUTH.md) | Fondasi, Database, & Autentikasi Admin | Setup DB, migrasi users/roles, login admin, Blade layout Slate & Blue. | ✅ **SELESAI** |
| [PHASE-02-DASHBOARD-AND-MEETING-MANAGEMENT.md](file:///D:/DIHADIR/development/PHASE-02-DASHBOARD-AND-MEETING-MANAGEMENT.md) | Dashboard Admin & Data Pertemuan | Sidebar navigasi 7 menu, ringkasan kartu statistik, CRUD Data Pertemuan & assignment. | ✅ **SELESAI** |
| [PHASE-03-PARTICIPANTS-AND-LOGIN-ABSENSI.md](file:///D:/DIHADIR/development/PHASE-03-PARTICIPANTS-AND-LOGIN-ABSENSI.md) | Data Peserta & Form Login Absensi | Manajemen data orang tua/pegawai (NIP), halaman Form Login Absensi dengan verifikasi geser (slide-to-verify). | ✅ **SELESAI** |
| [PHASE-04-ATTENDANCE-FORM-AND-SIGNATURE.md](file:///D:/DIHADIR/development/PHASE-04-ATTENDANCE-FORM-AND-SIGNATURE.md) | Halaman Absensi Pertemuan & Signature | Form absensi mobile-friendly, digital signature canvas, dropdown Kab/Kota searchable, token QR Code, anti-duplikasi. | ✅ **SELESAI** |
| [PHASE-05-REKAP-REPORTING-AND-EXPORT.md](file:///D:/DIHADIR/development/PHASE-05-REKAP-REPORTING-AND-EXPORT.md) | Rekap Kehadiran, Ekspor & Audit Log | Tabel rekap hadir/belum hadir, ekspor PDF/Excel ber-KOP sekolah, logging aktivitas. | ✅ **SELESAI** |
| [PHASE-06-SETTINGS-SECURITY-AND-DEPLOYMENT.md](file:///D:/DIHADIR/development/PHASE-06-SETTINGS-SECURITY-AND-DEPLOYMENT.md) | Pengaturan, Pengujian & Kesiapan Produksi | Konfigurasi sistem, security hardening, automated tests (53 tests passed), backup data, dan SOP deployment. | ✅ **SELESAI** |

---

## 3. Matriks Ketergantungan (Dependency Flow)

```
[Phase 01: Foundation & Auth]
        │
        ├──► [Phase 02: Dashboard & Meeting Management]
        │           │
        │           ▼
        ├──► [Phase 03: Participants & Form Login Absensi]
        │           │
        │           ▼
        └──► [Phase 04: Public Attendance Form & Signature Canvas]
                    │
                    ▼
             [Phase 05: Rekap Kehadiran, Laporan & Audit]
                    │
                    ▼
             [Phase 06: Settings, Hardening & Production]
```

---

## 4. Prinsip Kerja & Definition of Done

Setiap fase harus memenuhi kriteria berikut sebelum dianggap selesai:
1. **Fungsionalitas Terpenuhi:** Semua user story dan acceptance criteria berjalan mulus.
2. **Keamanan di Server:** Autentikasi, otorisasi (Gates/Policies), sanitasi input, dan rate limiting dieksekusi di backend.
3. **Design System Konsisten:** Tampilan mengikuti palet Slate & Blue, font Inter, dan mobile-friendly layout.
4. **Data Integrity:** Timestamp server `Asia/Jakarta`, penanganan transaksi database atomik, dan pencegahan duplikasi NIP per pertemuan.
5. **Clean URLs & Brand Identity:** Semua URL bersih tanpa ekstensi `.php`, dan antarmuka menggunakan aset logo resmi SMKN 1 Majalengka & DIHADIR.
