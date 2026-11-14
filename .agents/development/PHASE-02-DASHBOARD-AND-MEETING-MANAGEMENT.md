# FASE 02 — DASHBOARD ADMIN & DATA PERTEMUAN (MEETING MANAGEMENT)

Dokumen ini memuat spesifikasi teknis untuk implementasi antarmuka utama Dashboard Admin, navigasi Sidebar 7 menu, ringkasan metrik statistik kehadiran, serta modul CRUD Data Pertemuan (kegiatan).

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - DILARANG membuat data pertemuan fiktif, event contoh, atau data dummy statistik.
> - Saat pertama kali dijalankan, metrik dashboard wajib menampilkan angka `0` murni dari query database kosong.
> - Antarmuka tabel pertemuan wajib menampilkan komponen **Empty State** yang rapi dengan tombol aksi *"Tambah Pertemuan Baru"* ketika belum ada kegiatan yang dibuat oleh admin.
> - Seluruh fitur CRUD langsung terhubung dan siap dipakai untuk kegiatan nyata SMKN 1 Majalengka.

1. Mengimplementasikan antarmuka Dashboard Admin beserta 4 kartu ringkasan informasi utama.
2. Membangun komponen Sidebar Navigasi dengan 7 menu standar sesuai spesifikasi di [AGENTS.md](file:///D:/DIHADIR/AGENTS.md#L107-L125).
3. Mengembangkan modul **Data Pertemuan** (Event / Meeting Management): membuat, mengubah, menghapus, melihat detail, mengubah status, dan duplikasi kegiatan pertemuan.
4. Menerapkan kontrol kepemilikan/penugasan admin pada setiap kegiatan.

---

## 2. Rincian Teknis & Antarmuka

### 2.1 Sidebar Navigasi Admin (7 Menu Standar)

Komponen sidebar terletak pada `resources/views/components/admin/sidebar.blade.php`:

| No | Nama Menu | Rute / URL | Ikon Lucide | Deskripsi Fungsi |
|---|---|---|---|---|
| 1 | **Dashboard** | `route('admin.dashboard')` | `layout-dashboard` | Menampilkan ringkasan informasi absensi (jumlah peserta, kehadiran, pertemuan). |
| 2 | **Data Pertemuan** | `route('admin.meetings.index')` | `calendar-days` | Mengelola kegiatan pertemuan (nama kegiatan, tanggal, waktu, lokasi, status). |
| 3 | **Data Peserta** | `route('admin.participants.index')` | `users` | Menampilkan daftar orang tua atau peserta terdaftar & pengelolaan identitas. |
| 4 | **Rekap Kehadiran** | `route('admin.attendance.index')` | `clipboard-check` | Menampilkan daftar peserta yang sudah hadir dan belum hadir di tiap pertemuan. |
| 5 | **Laporan** | `route('admin.reports.index')` | `file-text` | Rekapitulasi laporan kehadiran yang dapat diunduh (PDF / Excel). |
| 6 | **Pengaturan** | `route('admin.settings.index')` | `settings` | Pengaturan informasi akun admin dan konfigurasi kebutuhan sistem. |
| 7 | **Logout** | `route('admin.logout')` | `log-out` | Form POST untuk mengakhiri sesi admin dan keluar dari dashboard. |

### 2.2 Ringkasan Dashboard (Summary Metric Cards)

Halaman utama dashboard (`/admin/dashboard`) menampilkan 4 metrik cards secara visual:
1. **Total Peserta:** Jumlah seluruh peserta/orang tua yang terdaftar pada sistem atau acara aktif.
2. **Sudah Hadir:** Jumlah peserta yang telah berhasil mengirimkan absensi.
3. **Belum Hadir:** Jumlah peserta terdaftar yang belum mengisi absensi (Total Peserta dikurangi Sudah Hadir).
4. **Total Pertemuan:** Jumlah seluruh kegiatan pertemuan yang tercatat di sistem.

Selain metrik cards, halaman dashboard menyajikan:
- Tabel pintas *Pertemuan Terbaru / Sedang Berlangsung*.
- Log aktivitas kehadiran masuk secara realtime / terkini.

### 2.3 Skema Database Data Pertemuan (`meetings` / `events`)

Migration `create_meetings_table`:
- `id` (bigIncrements)
- `title` (string): Nama kegiatan / pertemuan.
- `slug` (string, unique): Slug nama kegiatan.
- `public_token` (string, 32 char unique): Token unik acak untuk link publik dan QR Code (tidak mengekspos primary key).
- `description` (text, nullable): Deskripsi tujuan kegiatan.
- `organizer` (string): Penyelenggara kegiatan (default: SMKN 1 Majalengka).
- `location` (string): Lokasi atau ruangan kegiatan.
- `meeting_date` (date): Tanggal kegiatan.
- `start_time` (time): Waktu mulai kegiatan.
- `end_time` (time, nullable): Waktu selesai kegiatan.
- `max_participants` (integer, nullable): Batas maksimum peserta jika ada.
- `status` (enum: `draft`, `scheduled`, `ongoing`, `completed`, `archived` - default: `scheduled`).
- `form_status` (enum: `draft`, `published`, `closed` - default: `draft`).
- `allow_anonymous` (boolean, default false): Apakah absensi memerlukan login NIP atau terbuka langsung.
- `created_by` (foreignId -> `users.id`): Pembuat acara.
- `timestamps` & `softDeletes`

Tabel Pivot `meeting_admins`:
- `meeting_id` (foreignId -> `meetings.id`, onDelete cascade)
- `user_id` (foreignId -> `users.id`, onDelete cascade)

---

## 3. Langkah Implementasi (Task Checklist)

- [ ] Buat migration `create_meetings_table` dan `create_meeting_admins_table`.
- [ ] Buat Model `Meeting` dengan relasi Eloquent (`creator()`, `admins()`, `attendances()`, `scopeActive()`).
- [ ] Buat `MeetingPolicy` untuk memeriksa apakah admin yang login berwenang mengelola pertemuan terkait (Super Admin bypass).
- [ ] Buat Form Request:
  - `StoreMeetingRequest`
  - `UpdateMeetingRequest`
- [ ] Buat `Admin\DashboardController`:
  - Hitung agregasi metrik (Total Peserta, Sudah Hadir, Belum Hadir, Total Pertemuan).
  - Tampilkan list kegiatan hari ini dan kehadiran terbaru.
- [ ] Buat `Admin\MeetingController` (Resource Controller):
  - `index()`: Daftar pertemuan dengan search & filter status.
  - `create()` & `store()`: Form tambah pertemuan.
  - `show()`: Detail pertemuan, info link publik & QR Code, statistik absensi per acara.
  - `edit()` & `update()`: Form edit pertemuan.
  - `destroy()`: Hapus pertemuan (soft delete).
  - `duplicate(Meeting $meeting)`: Gandakan kegiatan untuk pertemuan berkala.
  - `toggleFormStatus(Meeting $meeting)`: Publikasi atau tutup formulir absensi.
- [ ] Buat View Blade:
  - `resources/views/admin/dashboard.blade.php`
  - `resources/views/admin/meetings/index.blade.php`
  - `resources/views/admin/meetings/create.blade.php`
  - `resources/views/admin/meetings/edit.blade.php`
  - `resources/views/admin/meetings/show.blade.php`

---

## 4. Kriteria Pengujian (Definition of Done)

1. Dashboard memuat 4 kartu metrik dengan angka nyata dari database (bukan nilai statis/palsu).
2. Sidebar menyorot menu aktif sesuai rute yang sedang dibuka (`active state`).
3. Admin dapat membuat pertemuan baru dan otomatis mendapatkan `public_token` unik.
4. Admin biasa tidak dapat mengakses, mengedit, atau menghapus pertemuan milik admin lain (HTTP 403).
5. Fitur ubah status pertemuan (`ongoing`, `completed`, `closed`) berfungsi dan tercatat di database.
