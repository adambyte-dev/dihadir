# FASE 04 — FORMULIR ABSENSI PERTEMUAN, TANDA TANGAN DIGITAL & QR CODE

Dokumen ini memuat spesifikasi teknis untuk **Halaman Absensi Pertemuan**, kanvas **Tanda Tangan Digital**, dropdown pencarian Kabupaten/Kota, pencatatan otomatis waktu server, generator QR Code, serta mekanisme **Pencegahan Pengisian Ganda**.

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - DILARANG membuat data kehadiran tiruan (*dummy attendance records*) atau gambar tanda tangan palsu di database/storage.
> - Data absensi hanya boleh masuk melalui pengisian formulir kehadiran nyata oleh peserta di lapangan.
> - Halaman formulir publik langsung terhubung dengan data kegiatan pertemuan riil yang diterbitkan admin.

1. Membangun halaman publik **Absensi Pertemuan** yang mobile-first, responsif, dan ringan untuk orang tua/peserta.
2. Mengembangkan komponen **Tanda Tangan Digital** berbasis HTML5 Canvas yang dapat dibubuhkan lewat layar sentuh HP maupun mouse laptop.
3. Menyediakan komponen dropdown pencarian (*searchable select*) untuk daftar Kabupaten/Kota.
4. Menerapkan pengisian otomatis tanggal & waktu kehadiran berdasarkan waktu server (`Asia/Jakarta`).
5. Membangun generator link publik dan QR Code untuk setiap kegiatan pertemuan yang diterbitkan.
6. Menerapkan mekanisme validasi ketat dan **Pencegahan Duplikasi Absensi** (satu peserta hanya dapat mengisi satu kali untuk kegiatan yang sama).

---

## 2. Rincian Teknis & Alur Pengisian

### 2.1 Struktur & Anatomi Halaman Absensi (`/p/{public_token}`)

> [!NOTE]
> **Clean URLs:** Tautan publik menggunakan format bersih `/p/{public_token}` tanpa embel-embel file `.php`.

#### A. Bagian Header (Identitas Pertemuan & Logo Resmi)
- **Branding Resmi:** Lambang resmi **SMKN 1 Majalengka** (`asset('images/logo-smkn1majalengka.png')`) dan logo aplikasi **DIHADIR** (`asset('images/logo-dihadir-full.png')`) ditampilkan di posisi atas halaman secara proporsional.
- Judul Halaman: **Absensi Pertemuan**.
- Kartu Identitas Kegiatan:
  - Nama Pertemuan: `Meeting::title`
  - Penyelenggara: `Meeting::organizer`
  - Tanggal & Waktu: Format Bahasa Indonesia (contoh: *Jumat, 15 Mei 2026, 08:30 WIB*)
  - Lokasi / Ruangan: `Meeting::location`
- Teks Panduan Singkat: *"Silakan periksa dan lengkapi data kehadiran Anda di bawah ini secara benar, lalu bubuhkan tanda tangan sebelum menekan tombol Kirim Absensi."*

#### B. Formulir Data Peserta
Formulir memuat field berikut:
1. **Nama Lengkap:** Input teks wajib. (Jika peserta telah terotentikasi via NIP, field terisi otomatis dan dapat diedit bila perlu konfirmasi ejaan).
2. **NIP (Nomor Induk Pegawai):** Input teks/nomor pegawai wajib.
3. **Unit Kerja:** Input teks wajib (tempat/instansi instansi asal).
4. **Kabupaten/Kota:** Komponen searchable dropdown berbasis Alpine.js dengan dataset daftar kabupaten/kota (prioritas Jawa Barat dan nasional) sehingga peserta cukup mengetik beberapa huruf untuk menemukan daerahnya.
5. **Tanggal Kehadiran:** Read-only / auto-filled (ditampilkan kepada pengguna sesuai tanggal saat form dibuka).
6. **Waktu Kehadiran:** Read-only / auto-filled (dicatat real-time).
7. **Tanda Tangan Digital:**
   - Komponen canvas interaktif (`signature_pad`).
   - Mendukung input sentuhan jari / stylus pada smartphone dan drag mouse pada laptop.
   - Tombol **"Hapus / Ulangi Tanda Tangan"** untuk mereset canvas bila terjadi kesalahan goresan.
   - Disimpan dalam bentuk base64 PNG yang diproses dan disimpan di private storage.

#### C. Pengiriman & Notifikasi Sukses
- Tombol **Kirim Absensi** dengan state pencegahan klik ganda (*double-submit prevention*) dan visual loading indicator.
- Setelah data tervalidasi dan tersimpan di database, halaman menampilkan tampilan konfirmasi sukses:
  - Ikon centang hijau besar (`badge-check`).
  - Ucapan terima kasih: *"Kehadiran Anda Telah Berhasil Dicatat."*
  - Ringkasan data yang telah dikirim (Nama, NIP, Waktu Masuk Server).
  - Tombol untuk kembali ke beranda atau menutup halaman.

---

### 2.2 Pencegahan Absensi Berulang (Anti-Duplicate Check)

Untuk menjaga validitas data:
1. Sebelum menyimpan ke tabel `attendances`, backend memeriksa apakah kombinasi `meeting_id` dan `nip` peserta sudah pernah terdaftar dengan status valid.
2. Jika ditemukan data sebelumnya:
   - Request ditolak dengan pesan: *"NIP Anda telah tercatat melakukan absensi pada pertemuan ini pada [Jam:Menit WIB]. Pengisian ulang tidak diperkenankan."*
   - Menghindari kecurangan kehadiran ganda.

---

### 2.3 Skema Database Kehadiran (`attendances`)

Migration `create_attendances_table`:
- `id` (bigIncrements)
- `meeting_id` (foreignId -> `meetings.id`, index)
- `participant_id` (foreignId -> `participants.id`, nullable)
- `nip` (string, index)
- `name` (string)
- `unit_kerja` (string)
- `kabupaten_kota` (string)
- `signature_path` (string): Path file tanda tangan di private storage.
- `attended_date` (date): Tanggal kehadiran (server date).
- `attended_time` (time): Waktu kehadiran (server time).
- `ip_address` (string, nullable)
- `user_agent` (text, nullable)
- `status` (enum: `valid`, `flagged`, `cancelled` - default: `valid`)
- `timestamps`
- Unique constraint: `['meeting_id', 'nip']`

---

### 2.4 QR Code & Public Link

- Setiap pertemuan memiliki URL publik: `url('/p/' . $meeting->public_token)`.
- Pada detail pertemuan di dashboard admin, sediakan fitur:
  - Generate SVG/PNG QR Code menggunakan package lokal yang aman (misalnya `chillerlan/php-qrcode` atau `simplesoftwareio/simple-qrcode`).
  - Tombol **Salin Tautan Publik**.
  - Tombol **Unduh QR Code** (PNG beresolusi tinggi untuk dicetak di banner atau proyektor ruangan).

---

## 3. Langkah Implementasi (Task Checklist)

- [ ] Buat migration `create_attendances_table` dengan index dan unique key `[meeting_id, nip]`.
- [ ] Buat Model `Attendance` dengan relasi ke `Meeting` dan `Participant`.
- [ ] Buat Form Request `StoreAttendanceRequest` (validasi wajib: nama, nip, unit_kerja, kabupaten_kota, signature_data).
- [ ] Buat Komponen Blade Canvas Tanda Tangan: `resources/views/components/signature-canvas.blade.php`.
- [ ] Buat Komponen Searchable Select Kabupaten/Kota: `resources/views/components/searchable-select.blade.php` dengan data referensi wilayah Indonesia.
- [ ] Buat `PublicAttendanceController`:
  - `show($public_token)`: Tampilkan form absensi pertemuan jika status `published` dan pertemuan masih berlangsung.
  - `submit(StoreAttendanceRequest $request, $public_token)`: Simpan data absensi, simpan gambar tanda tangan, proteksi transaksi DB.
  - `success($public_token)`: Tampilan halaman konfirmasi sukses.
- [ ] Buat service untuk generate QR Code: `App\Services\QrCodeService`.
- [ ] Buat View Blade:
  - `resources/views/public/attendance-form.blade.php`
  - `resources/views/public/attendance-success.blade.php`
  - `resources/views/public/attendance-closed.blade.php`

---

## 4. Kriteria Pengujian (Definition of Done)

1. Form absensi dapat diisi dan dibubuhkan tanda tangan dengan lancar melalui layar sentuh smartphone maupun browser laptop.
2. Pengisian berulang dengan NIP yang sama pada pertemuan yang sama ditolak oleh validasi server.
3. Waktu kehadiran yang tersimpan di database mencerminkan waktu penerimaan server `Asia/Jakarta`, bukan jam lokal browser client.
4. Tanda tangan digital tersimpan sebagai gambar terenkripsi/privat di storage dan tidak terekspos langsung tanpa otorisasi.
5. Scan QR Code pertemuan langsung membuka halaman form absensi yang tepat.
