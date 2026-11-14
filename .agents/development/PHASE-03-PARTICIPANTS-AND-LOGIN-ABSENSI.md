# FASE 03 — DATA PESERTA & FORM LOGIN ABSENSI (DENGAN VERIFIKASI GESER)

Dokumen ini memuat spesifikasi teknis untuk pengelolaan Data Peserta (orang tua / pegawai) serta pembangunan halaman khusus **Form Login Absensi** yang dilengkapi fitur interaktif *Verifikasi Geser (Slide-to-Verify)* sebelum peserta mengisi kehadiran pertemuan.

---

## 1. Tujuan & Ruang Lingkup

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO DATA — LANGSUNG SIAP PRODUKSI**
> - DILARANG KERAS membuat peserta tiruan, akun dummy orang tua, atau data NIP palsu di database.
> - Halaman Data Peserta harus menampilkan komponen **Empty State** yang informatif dengan panduan menambahkan peserta secara manual atau mengunggah berkas Excel/CSV riil.
> - Fitur impor Excel/CSV dan login absensi langsung siap memproses data pegawai/orang tua riil SMKN 1 Majalengka.

1. Membangun modul manajemen **Data Peserta** (CRUD data peserta, impor massal dari Excel/CSV, pencarian, dan filtering).
2. Membangun halaman antarmuka **Form Login Absensi** untuk peserta kegiatan sesuai spesifikasi di [AGENTS.md](file:///D:/DIHADIR/AGENTS.md#L153-L163).
3. Menerapkan fitur keamanan interaktif **Verifikasi Geser** (*slide to unlock / verify bot-prevention*) berbasis Alpine.js dengan validasi token keamanan di sisi backend.
4. Menyiapkan sesi autentikasi peserta berbasis NIP agar peserta langsung diarahkan ke halaman absensi pertemuan terkait.

---

## 2. Rincian Teknis & Antarmuka

### 2.1 Modul Data Peserta (Dashboard Admin)

Menu Sidebar: **Data Peserta** (`/admin/participants`).
Admin dapat mengontrol data seluruh peserta yang diundang atau terdaftar dalam kegiatan.

#### Skema Database `participants`
- `id` (bigIncrements)
- `nip` (string, unique): Nomor Induk Pegawai sebagai identitas utama.
- `name` (string): Nama lengkap orang tua / peserta.
- `unit_kerja` (string): Tempat / instansi bekerja.
- `kabupaten_kota` (string, nullable): Asal kabupaten/kota.
- `phone_number` (string, nullable): Nomor WhatsApp/telepon peserta.
- `password` (string): Kata sandi terenkripsi (default format: NIP atau tanggal lahir, dapat diubah).
- `is_active` (boolean, default true).
- `timestamps` & `softDeletes`.

Tabel Pivot Pendaftaran Peserta ke Pertemuan `meeting_participants`:
- `meeting_id` (foreignId -> `meetings.id`)
- `participant_id` (foreignId -> `participants.id`)
- `status`: `invited`, `attended`, `absent` (default: `invited`)

---

### 2.2 Form Login Absensi (Spesifikasi Halaman)

Halaman ini dapat diakses pada URL publik `/absensi/login` atau `/absensi/{meeting_token}/login` (**Clean URL tanpa ekstensi `.php`**).

Halaman didesain bersih, fokus, dan mobile-first dengan komponen:

1. **Header Branding & Identitas Logo Resmi:**
   - Menampilkan lambang resmi **SMKN 1 Majalengka** (`asset('images/logo-smkn1majalengka.png')`) berdampingan dengan logo **DIHADIR** (`asset('images/logo-dihadir-full.png')`).
2. **Tombol Beranda (Pojok Kanan Atas):**
   - Ikon `home` + teks "Beranda".
   - Mengarahkan kembali ke landing page utama sistem (`/`).
3. **Judul & Keterangan Panduan:**
   - Judul: **Masuk Absensi Pertemuan**
   - Panduan: *"Silakan masukkan NIP dan kata sandi Anda yang telah terdaftar untuk membuka formulir kehadiran pertemuan."*
4. **Kolom NIP (Nomor Induk Pegawai):**
   - Input tipe teks dengan validasi angka/karakter NIP.
   - Label jelas: "NIP (Nomor Induk Pegawai)".
   - Placeholder: "Contoh: 198501152010011002".
5. **Kolom Password:**
   - Input tipe password dengan toggle visibility (mata buka/tutup).
   - Label: "Kata Sandi".
6. **Verifikasi Geser (Slide-to-Verify):**
   - Komponen interaktif slider berbasis Alpine.js:
     - Track horizontal dengan teks petunjuk: *"Geser ke kanan untuk verifikasi"*
     - Handle tombol geser dengan ikon panah (`arrow-right`) yang berubah menjadi centang hijau (`check`) saat posisi mencapai ujung kanan (100%).
     - State `isVerified = true` mengaktifkan field hidden `slider_token` dan membuka status tombol submit.
     - Proteksi: Tombol login tetap berstatus `disabled` jika verifikasi geser belum diselesaikan secara tuntas.
7. **Tombol Login:**
   - Tombol utama berwarna aksen Blue-500 (`#3B82F6`) bertuliskan **Masuk ke Absensi**.
   - Dilengkapi *loading spinner state* saat data NIP & password sedang diproses.
8. **Tautan Lupa Password?:**
   - Tautan teks halus di bawah form: *"Lupa kata sandi? Hubungi Panitia / IT Support SMKN 1 Majalengka"*.
   - Membuka modal panduan bantuan atau kontak narahubung panitia.

---

## 3. Langkah Implementasi (Task Checklist)

- [ ] Buat migration: `create_participants_table` dan `create_meeting_participants_table`.
- [ ] Buat Model `Participant` dengan autentikasi Guard `participant` (mendukung otentikasi sesi mandiri terpisah dari admin).
- [ ] Buat `Admin\ParticipantController`:
  - `index()`: Tabel peserta dengan fitur search NIP/nama, pagination, dan filter.
  - `create()`, `store()`: Input manual peserta baru.
  - `edit()`, `update()`, `destroy()`: Pembaruan data peserta & reset password.
  - `import()`: Impor data peserta via format CSV / XLSX.
- [ ] Buat Komponen Blade Slider: `resources/views/components/slide-to-verify.blade.php`:
  - Penanganan touch event (mobile) dan mouse drag event (desktop).
  - Penghasilan token sesi verifikasi satu kali pakai.
- [ ] Buat `Auth\ParticipantLoginController`:
  - `showLoginForm($token = null)`: Menampilkan form login absensi (bila ada `$token`, tautkan langsung ke pertemuan target).
  - `login(ParticipantLoginRequest $request)`: Verifikasi kredensial NIP + password + token slider.
  - `logout()`: Mengakhiri sesi peserta.
- [ ] Buat View Blade: `resources/views/auth/participant-login.blade.php`.

---

## 4. Kriteria Pengujian (Definition of Done)

1. Form Login Absensi tidak dapat disubmit sebelum slider verifikasi digeser hingga ujung kanan (100%).
2. Peserta dengan NIP dan kata sandi yang valid berhasil login dan dialihkan langsung ke formulir absensi pertemuan yang dituju.
3. Upaya login dengan NIP yang salah memunculkan pesan error bahasa Indonesia yang jelas tanpa membocorkan eksistensi data sensitif.
4. Desain form login tampil prima dan mudah digunakan pada layar smartphone (layar sentuh) maupun browser laptop/desktop.
