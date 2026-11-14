# FASE 05 — REKAP KEHADIRAN, PELAPORAN (PDF/EXCEL) & AUDIT LOG

Dokumen ini memuat spesifikasi teknis untuk modul **Rekap Kehadiran** (daftar sudah hadir vs belum hadir), mesin **Pelaporan & Ekspor** (PDF ber-KOP resmi SMKN 1 Majalengka, Excel/XLSX, CSV), halaman ramah cetak (*print-friendly*), serta pencatatan jejak audit (*Audit Log*).

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - DILARANG membuat data rekap kehadiran palsu atau dokumen laporan demo dengan nama peserta fiktif.
> - Halaman Rekap Kehadiran wajib menampilkan **Empty State** jika belum ada peserta yang hadir pada pertemuan yang dipilih.
> - Generator PDF dan Excel langsung mengolah data absensi nyata dengan KOP resmi SMKN 1 Majalengka dan siap diserahkan kepada pihak berwenang sekolah.

1. Mengembangkan halaman **Rekap Kehadiran** (`/admin/attendance`) untuk memantau kehadiran secara realtime per kegiatan pertemuan.
2. Memfasilitasi pemisahan visual antara peserta yang **Sudah Hadir** dan **Belum Hadir**.
3. Membangun modul **Laporan** (`/admin/reports`) dengan opsi unduh PDF dan Excel/CSV berstandar administrasi sekolah resmi.
4. Menyertakan gambar tanda tangan digital peserta pada hasil cetak dan berkas PDF.
5. Mengamankan ekspor berkas spreadsheet dari bahaya *CSV/Formula Injection*.
6. Mengimplementasikan sistem **Audit Log** untuk mencatat tindakan krusial admin dan pengelolaan data.

---

## 2. Rincian Teknis & Alur Pelaporan

### 2.1 Modul Rekap Kehadiran (`/admin/attendance`)

Menu Sidebar: **Rekap Kehadiran**

#### Fitur & Antarmuka:
- **Filter Pertemuan:** Dropdown pemilihan kegiatan pertemuan (aktif, selesai, atau diarsipkan).
- **Tab Segmentasi:**
  - Tab 1: **Sudah Hadir** — Menampilkan daftar peserta yang telah melakukan absensi lengkap dengan waktu server, unit kerja, kabupaten/kota, dan thumbnail tanda tangan digital.
  - Tab 2: **Belum Hadir** — Menampilkan daftar peserta terdaftar/terundang yang belum mengisi formulir kehadiran.
- **Pencarian Cepat:** Filter nama, NIP, atau instansi/unit kerja secara realtime.
- **Modal Preview Tanda Tangan:** Menampilkan tanda tangan digital beresolusi jelas saat thumbnail diklik oleh admin.
- **Aksi Admin:** Kemampuan menandai kehadiran yang mencurigakan (*flagged*) atau membatalkan respons (*cancelled*) disertai alasan yang masuk ke audit log.

---

### 2.2 Modul Laporan & Ekspor (`/admin/reports`)

Menu Sidebar: **Laporan**

#### Format Laporan Standar Sekolah:
1. **Header / KOP Resmi Sekolah:**
   - Logo Lambang Resmi SMKN 1 Majalengka (`public/images/logo-smkn1majalengka.png`) beresolusi tajam di sisi kiri KOP.
   - Teks KOP: Pemerintah Daerah Provinsi Jawa Barat — Dinas Pendidikan — Cabang Dinas Wilayah IX — SMKN 1 Majalengka.
   - Watermark atau footer logo DIHADIR (`public/images/logo-dihadir-icon.png`).
2. **Informasi Kegiatan:**
   - Nama Acara / Pertemuan
   - Tanggal dan Waktu Pelaksanaan
   - Tempat / Ruangan
   - Penyelenggara
3. **Ringkasan Kehadiran:**
   - Total Peserta Terdaftar
   - Jumlah Peserta Hadir (dengan persentase)
   - Jumlah Peserta Tidak Hadir
4. **Tabel Data Kehadiran:**
   - No
   - NIP
   - Nama Lengkap
   - Unit Kerja / Instansi
   - Kabupaten / Kota
   - Waktu Kehadiran (WIB)
   - Kolom Tanda Tangan (menampilkan gambar tanda tangan digital)
5. **Footer Lembar Pengesahan:**
   - Tempat, tanggal cetak laporan.
   - Kolom tanda tangan Kepala SMKN 1 Majalengka dan/atau Ketua Panitia Kegiatan.

#### Tipe Format Ekspor:
- **PDF:** Menggunakan generator PDF Laravel yang terawat (misalnya `barryvdh/laravel-dompdf`). Layout presisi A4 Portrait/Landscape.
- **Excel / XLSX:** Menggunakan generator spreadsheet (misalnya `maatwebsite/excel` atau writer native).
- **CSV:** Berkas raw CSV dengan pembersihan tanda baca karakter pembuka seperti `=`, `+`, `-`, `@` untuk mencegah *Formula Injection*.
- **Print View:** Rute HTML bersih khusus cetak browser (`window.print()`) dengan CSS `@media print`.

---

### 2.3 Skema Database Audit Log (`audit_logs`)

Migration `create_audit_logs_table`:
- `id` (bigIncrements)
- `user_id` (foreignId -> `users.id`, nullable)
- `action` (string): Contoh `meeting_created`, `meeting_updated`, `form_published`, `attendance_cancelled`, `report_exported`.
- `target_type` (string): Nama model terkait (`Meeting`, `Attendance`, dsb).
- `target_id` (unsignedBigInteger, nullable)
- `ip_address` (string, nullable)
- `user_agent` (text, nullable)
- `details` (json, nullable): Payload perubahan (before/after snapshot).
- `created_at` (timestamp server)

---

## 3. Langkah Implementasi (Task Checklist)

- [ ] Buat migration `create_audit_logs_table`.
- [ ] Buat Model `AuditLog` dan Service `App\Services\AuditLogger`.
- [ ] Buat `Admin\AttendanceRecapController`:
  - `index(Request $request)`: Filter pertemuan & pagination daftar hadir/belum hadir.
  - `showSignature(Attendance $attendance)`: Endpoint aman untuk melihat file tanda tangan privat dengan otorisasi.
  - `flag(Attendance $attendance)`: Tandai kehadiran.
- [ ] Pasang pustaka PDF dan Excel pada project sesuai kebutuhan:
  - `composer require barryvdh/laravel-dompdf` (atau renderer PDF yang telah ditentukan).
- [ ] Buat `Admin\ReportController`:
  - `index()`: Tampilan seleksi laporan per pertemuan.
  - `exportPdf(Meeting $meeting)`: Stream / unduh PDF ber-KOP resmi.
  - `exportExcel(Meeting $meeting)`: Unduh file XLSX terstruktur.
  - `exportCsv(Meeting $meeting)`: Unduh file CSV dengan sanitasi formula injection.
  - `printView(Meeting $meeting)`: Tampilan HTML print-friendly.
- [ ] Buat View Blade:
  - `resources/views/admin/attendance/index.blade.php`
  - `resources/views/admin/reports/index.blade.php`
  - `resources/views/admin/reports/pdf-template.blade.php`
  - `resources/views/admin/reports/print-preview.blade.php`

---

## 4. Kriteria Pengujian (Definition of Done)

1. Rekap kehadiran memfilter data secara akurat sesuai pertemuan yang dipilih.
2. Tab "Belum Hadir" menghitung selisih peserta yang diundang dengan yang telah mengisi absensi.
3. Ekspor PDF menghasilkan dokumen rapi ber-KOP SMKN 1 Majalengka, menyertakan gambar tanda tangan digital tanpa pecah gambar, dan proporsional dalam 1 atau beberapa halaman A4.
4. Ekspor Excel/CSV tidak dapat mengeksekusi rumus berbahaya (*formula injection safe*).
5. Setiap tindakan ekspor laporan dan perubahan status kehadiran terekam di tabel `audit_logs`.
