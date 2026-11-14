# DIHADIR — Digital Attendance Management System

<p align="center">
  <strong>Sistem Pengelolaan Kehadiran Digital untuk Kegiatan & Pertemuan Sekolah</strong><br>
  <em>SMK Negeri 1 Majalengka</em>
</p>

---

## 📌 Ringkasan Produk

**DIHADIR** adalah platform manajemen presensi digital yang dirancang untuk menggantikan absensi manual berbasis kertas pada berbagai agenda kegiatan pertemuan sekolah (rapat dinas guru, pertemuan orang tua/wali murid, komite, dan workshop kedinasan).

Sistem ini memudahkan penyelenggara dalam mempublikasikan formulir absensi digital melalui tautan langsung atau QR Code statis/dinamis, memverifikasi identitas peserta (NIP/NIK), merekam tanda tangan digital langsung dari layar sentuh maupun pointer mouse, mencegah absensi ganda, serta menyusun rekap dan laporan kehadiran dalam format PDF dan Excel ber-KOP resmi.

### 🌟 Prinsip Desain & Produk
- **Simple & Minimalist:** Antarmuka bersih, fokus pada tugas pengguna tanpa ornamen dekoratif berlebihan.
- **Mobile-First & Responsive:** Formulir kehadiran mudah diisi dari smartphone peserta tanpa kendala layout overflow atau tap target yang sempit.
- **Reliable & Audit-Ready:** Waktu kehadiran (*timestamp*) dicatat murni berdasarkan jam server (`Asia/Jakarta`), bukan jam perangkat peserta.
- **Zero Demo Data:** Database dalam kondisi bersih siap produksi (*clean production-ready state*) tanpa data tiruan atau seeder palsu.

---

## 🛠️ Tech Stack & Ekosistem

- **Backend:** [Laravel 12](https://laravel.com) (PHP 8.2+)
- **Database:** SQLite (Development) / PostgreSQL (Production)
- **Frontend & Views:** Laravel Blade, Alpine.js, Tailwind CSS v4
- **UI Components:** [shadcn UI](https://ui.shadcn.com) (`radix-mira` preset `b1D0dv72` with Radix UI primitives & Lucide Icons)
- **Aset & Build Tool:** [Vite](https://vitejs.dev)
- **Tipografi:** [Inter](https://rsms.me/inter/) font family
- **Desain Warna (Slate & Blue):**
  - **Primary:** `#334155` (Slate-700)
  - **Accent / Action:** `#3B82F6` (Blue-500)
  - **Surface:** `#FFFFFF`
  - **Background:** `#F8FAFC` (Slate-50)
  - **Text:** `#0F172A` (Slate-900)
  - **Border:** `#CBD5E1` (Slate-300)

---

## 👥 Role & Hak Akses

Sistem dibatasi secara ketat pada **maksimal 3 peran pengguna**:

1. **Super Admin (Pengelola Utama):**
   - Akses penuh ke seluruh data pertemuan dan sistem.
   - Mengelola akun Admin dan penugasan pertemuan.
   - Memantau *Audit Log* dan pengaturan aplikasi.
2. **Admin / Pengelola Acara:**
   - Mengelola acara/pertemuan yang ditugaskan kepadanya.
   - Mengatur status pertemuan (`draft`, `scheduled`, `ongoing`, `completed`, `archived`).
   - Mengunduh / membagikan QR Code dan link absensi.
   - Memantau kehadiran peserta dan mengekspor rekap laporan (PDF / Excel).
3. **Peserta (Orang Tua / Guru / Pegawai):**
   - Mengakses formulir absensi melalui tautan/QR Code atau halaman **Form Login Absensi** (NIP, password, verifikasi geser *slide-to-verify*).
   - Mengisi Nama Lengkap, NIP/Identitas, Unit Kerja, Kabupaten/Kota, dan membubuhkan Tanda Tangan Digital.
   - Terlindungi oleh sistem pencegahan absensi ganda pada pertemuan yang sama.

---

## 📋 Modul Utama Aplikasi

1. **Dashboard Admin:** Ringkasan statistik operasional riil (Total Pertemuan, Total Peserta, Rata-rata Kehadiran, Laporan Tersedia), grafik tren kehadiran enam bulan terakhir, daftar pertemuan terbaru dengan persentase kehadiran, diagram donat komposisi status respons, serta panel informasi operasional. Seluruh angka dihitung dari data nyata — tanpa data contoh.
2. **Data Pertemuan:** Manajemen informasi kegiatan, tanggal, waktu, lokasi, penyelenggara, dan status pertemuan.
3. **Data Peserta:** Pengelolaan identitas peserta/orang tua yang terdaftar pada instansi.
4. **Halaman Absensi Pertemuan:** Formulir kehadiran publik mobile-first yang dilengkapi tanda tangan digital interaktif di layar.
5. **Form Login Absensi:** Halaman otentikasi peserta dengan proteksi bot berupa slider interaktif (*slide to verify*).
6. **Rekap & Laporan Kehadiran:** Pemantauan daftar hadir *real-time* serta ekspor dokumen PDF dan XLSX yang siap cetak.
7. **Pengaturan:** Manajemen profil admin dan pengaturan institusi (khusus Super Admin untuk bagian sistem).
8. **Audit Log:** Jejak aktivitas sistem yang hanya-baca dan hanya dapat diakses Super Admin — mencatat aktor, aksi, objek terdampak, alamat IP, dan waktu kejadian. Catatan tidak dapat diubah atau dihapus dari panel.

### 📱 Perilaku Navigasi Responsif

| Breakpoint | Navigasi | Catatan |
|---|---|---|
| Desktop (`≥1024px`) | Sidebar tetap 272 px dengan identitas sekolah, empat kelompok menu, kartu akun, dan tombol keluar. | Area konten memakai lebar penuh yang tersisa, tanpa batas `max-width`. |
| Mobile (`<1024px`) | Tanpa sidebar. **Bottom navigation** 5 tab (Dashboard, Pertemuan, Peserta, Rekap, Laporan) + sheet "menu lainnya" untuk seluruh menu lain. | Tinggi tab 58 px, `safe-area-inset-bottom` dihormati, konten diberi `padding-bottom` agar tidak tertutup. Tidak ada scroll horizontal. |


---

## 📂 Struktur Roadmap Pengembangan (`development/`)

Seluruh implementasi fitur mengikuti rencana kerja bertahap yang telah disusun pada direktori [development/](file:///D:/DIHADIR/development):

| Fase | Dokumen Acuan | Lingkup Kerja |
|---|---|---|
| **Roadmap Utama** | [00-ROADMAP.md](file:///D:/DIHADIR/development/00-ROADMAP.md) | Gambaran arsitektur, dependency flow, dan *definition of done*. |
| **Fase 1** | [PHASE-01-FOUNDATION-AND-AUTH.md](file:///D:/DIHADIR/development/PHASE-01-FOUNDATION-AND-AUTH.md) | Fondasi Laravel, migrasi tabel user/role, layout Blade Slate & Blue, dan auth admin. |
| **Fase 2** | [PHASE-02-DASHBOARD-AND-MEETING-MANAGEMENT.md](file:///D:/DIHADIR/development/PHASE-02-DASHBOARD-AND-MEETING-MANAGEMENT.md) | Dashboard ringkasan kartu statistik, navigasi sidebar 7 menu, dan CRUD Pertemuan. |
| **Fase 3** | [PHASE-03-PARTICIPANTS-AND-LOGIN-ABSENSI.md](file:///D:/DIHADIR/development/PHASE-03-PARTICIPANTS-AND-LOGIN-ABSENSI.md) | Manajemen Data Peserta dan halaman Form Login Absensi (slider captcha). |
| **Fase 4** | [PHASE-04-ATTENDANCE-FORM-AND-SIGNATURE.md](file:///D:/DIHADIR/development/PHASE-04-ATTENDANCE-FORM-AND-SIGNATURE.md) | Halaman Absensi Pertemuan publik, signature pad canvas, dan anti-duplicate check. |
| **Fase 5** | [PHASE-05-REKAP-REPORTING-AND-EXPORT.md](file:///D:/DIHADIR/development/PHASE-05-REKAP-REPORTING-AND-EXPORT.md) | Rekap kehadiran, audit logging, serta ekspor PDF dan XLSX. |
| **Fase 6** | [PHASE-06-SETTINGS-SECURITY-AND-DEPLOYMENT.md](file:///D:/DIHADIR/development/PHASE-06-SETTINGS-SECURITY-AND-DEPLOYMENT.md) | Pengaturan institusi, automated testing, hardening keamanan, dan panduan produksi. |


---

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

### Persyaratan Sistem
- PHP 8.2 atau lebih baru (dengan ekstensi `pdo`, `mbstring`, `openssl`, `zip`, `intl`, `fileinfo`)
- Composer 2.x
- Node.js 18+ dan NPM

### Langkah Instalasi
1. **Clone repository:**
   ```bash
   git clone <repository-url> dihadir
   cd dihadir
   ```

2. **Instal dependensi backend:**
   ```bash
   composer install
   ```

3. **Instal dependensi frontend:**
   ```bash
   npm install
   ```

4. **Konfigurasi Environment:**
   Salin file `.env.example` menjadi `.env` lalu generate key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Jalankan Migrasi Database:**
   ```bash
   php artisan migrate
   ```

6. **Buat Akun Super Admin Awal:**
   Isi `SUPERADMIN_NAME`, `SUPERADMIN_EMAIL`, dan `SUPERADMIN_PASSWORD` pada `.env` (minimal 8 karakter), lalu jalankan:
   ```bash
   php artisan db:seed --class=SuperAdminSeeder
   ```
   Bila `SUPERADMIN_PASSWORD` dikosongkan, seeder membuat kata sandi acak dan menampilkannya satu kali di terminal. Tidak ada data contoh/dummy yang dibuat sistem.

7. **Build Aset Frontend:**
   ```bash
   npm run build
   # Atau untuk live reload saat pengembangan:
   # npm run dev
   ```

8. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Buka browser di `http://127.0.0.1:8000`.

### Checklist Keamanan Produksi
- `APP_ENV=production`, `APP_DEBUG=false`, dan `APP_URL` memakai `https://`.
- `SESSION_SECURE_COOKIE=true`, `SESSION_ENCRYPT=true`, serta `SESSION_DRIVER` bukan `array`.
- Ganti kata sandi Super Admin awal setelah login pertama, lalu hapus `SUPERADMIN_PASSWORD` dari `.env`.
- Jalankan `php artisan config:cache`, `route:cache`, dan `view:cache` setelah konfigurasi final.
- Aktifkan HTTPS pada web server; aplikasi memaksa skema HTTPS dan mengirim HSTS di semua response produksi.
- Batasi akses `storage/` dan pastikan direktori privat tidak dapat diakses langsung dari browser.
- Jadwalkan backup database dan uji prosedur pemulihan secara berkala.

---

## 👨‍💻 Tentang Pengembang

Aplikasi ini dirancang dan dikembangkan oleh:

**Muhammad Adam Hidayat (AdamByte)**  
Fullstack Developer • Network Enthusiast  
Siswa Teknik Jaringan Komputer dan Telekomunikasi (TJKT) di SMK Negeri 1 Majalengka.

> *"Membangun sistem yang handal bukan hanya tentang baris kode yang ditulis, melainkan tentang bagaimana setiap subsistem bekerja selaras untuk memecahkan masalah nyata secara aman, transparan, dan terukur."*

- **GitHub:** [@adambyte-dev](https://github.com/adambyte-dev)
- **Instagram:** [@adambyte.dev](https://instagram.com/adambyte.dev)
- **Email:** [mail.adamhidayat@gmail.com](mailto:mail.adamhidayat@gmail.com)

---

## 📄 Lisensi, Hak Cipta, & Disclaimer

Proyek ini dilisensikan di bawah lisensi **MIT License**.  
Hak Cipta Desain Sistem & Spesifikasi © 2026 Tim Pengembang Sistem Informasi Sekolah — SMKN 1 Majalengka.

**Disclaimer:** Sistem ini dibangun untuk mendukung transparansi dan efisiensi manajemen sarana prasarana sekolah secara digital. Segala bentuk implementasi pada skala produksi wajib mengikuti regulasi pengadaan barang dan jasa instansi terkait.
