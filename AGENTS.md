# AGENTS.md — DIHADIR

Panduan kerja untuk AI coding agent dan developer yang mengembangkan **DIHADIR (Digital Attendance Management System)**, sistem pengelolaan kehadiran digital untuk kegiatan di lingkungan SMKN 1 Majalengka.

Dokumen ini menjadi acuan utama untuk arsitektur aplikasi, aturan bisnis, keamanan, pengalaman pengguna, dan konsistensi visual. Ikuti instruksi ini ketika membaca, membuat, atau mengubah kode di repository.

---

## 1. Ringkasan Produk

DIHADIR digunakan untuk membuat dan mengelola acara, menyusun formulir kehadiran yang dapat dikustomisasi, membagikan formulir melalui tautan atau QR Code, menerima respons peserta, serta menghasilkan rekap dan laporan.

Alur utama produk:

1. Admin membuat acara.
2. Admin menyusun formulir kehadiran atau memilih template.
3. Admin memeriksa preview lalu menerbitkan formulir.
4. Sistem menyediakan tautan publik dan QR Code.
5. Peserta membuka formulir dan mengirimkan kehadiran tanpa harus membuat akun.
6. Sistem menyimpan respons beserta waktu penerimaan dari server.
7. Admin memantau, mengelola, dan mengekspor data kehadiran.

### Prinsip produk

- **Simple:** antarmuka mudah dipahami dan tidak memiliki menu yang berlebihan.
- **Flexible:** formulir dapat disesuaikan dengan kebutuhan setiap acara.
- **Reliable:** data tersimpan konsisten dan dapat ditelusuri.
- **Fast:** pengisian nyaman melalui ponsel maupun desktop.
- **Professional:** laporan rapi dan layak digunakan untuk administrasi.

Jangan mengembangkan DIHADIR menjadi sistem yang lebih kompleks dari kebutuhan kehadiran acara tanpa alasan produk yang jelas.

---

## 2. Teknologi dan Pendekatan Implementasi

### Teknologi utama

- **Backend:** Laravel.
- **Database:** gunakan database relasional yang telah dipilih atau tersedia pada repository; PostgreSQL direkomendasikan untuk instalasi baru.
- **Frontend:** utamakan Laravel Blade dan komponen UI yang konsisten dengan struktur project.
- **CSS:** Tailwind CSS jika sudah digunakan atau dipilih sebagai styling utama.
- **Interaksi ringan:** Alpine.js dapat digunakan bila diperlukan dan sesuai dengan stack yang sudah tersedia.
- **Font:** Inter.

### Aturan pemilihan teknologi

- Periksa `composer.json`, `package.json`, konfigurasi, dan struktur repository sebelum mengubah stack.
- Ikuti versi Laravel dan dependency yang sudah digunakan project. Jangan menaikkan versi framework atau mengganti dependency utama tanpa kebutuhan yang jelas.
- Untuk project Laravel baru, gunakan struktur Laravel konvensional dan modular. Jangan menambahkan SPA framework, microservices, atau infrastruktur rumit hanya untuk kebutuhan yang bisa ditangani Laravel.
- Gunakan Laravel migrations, Eloquent, Form Requests, Policies/Gates, queues, notifications, dan filesystem sesuai kebutuhan.
- Utamakan fitur bawaan Laravel atau package yang terawat. Jangan membuat abstraksi sendiri jika solusi bawaan sudah memadai.
- Jangan mengasumsikan Next.js, Golang, atau backend terpisah sebagai stack DIHADIR. Laravel adalah backend dan fondasi aplikasi utama.

---

## 3. Role dan Hak Akses

DIHADIR memiliki **maksimal tiga role**. Jangan menambahkan role baru tanpa persetujuan eksplisit pemilik produk.

### 3.1 Super Admin

Pengelola utama sistem dengan akses lintas acara.

- Mengelola akun Admin.
- Melihat dan mengelola seluruh acara.
- Mengelola konfigurasi sistem dan identitas aplikasi.
- Mengakses seluruh data kehadiran sesuai kebutuhan operasional.
- Melihat audit log.
- Mengelola pengaturan keamanan dan kebijakan sistem.

### 3.2 Admin / Pengelola Acara

Pengelola acara yang ditugaskan kepadanya.

- Membuat dan mengelola acara.
- Membuat, mengedit, dan menerbitkan formulir.
- Mengatur periode pengisian dan batas peserta.
- Membuat atau mengunduh QR Code.
- Melihat dan mengelola respons pada acara yang menjadi tanggung jawabnya.
- Mengekspor laporan acara yang dikelolanya.

Admin hanya boleh mengakses acara yang ditugaskan kepadanya. Terapkan pembatasan ini di backend, bukan hanya dengan menyembunyikan tombol atau menu pada frontend.

### 3.3 Peserta

Peserta merupakan orang tua siswa atau pegawai yang mengikuti kegiatan pertemuan.

- Mengakses halaman pertemuan secara langsung melalui tautan/QR Code atau melalui **Form Login Absensi** (memasukkan NIP, kata sandi terdaftar, dan verifikasi geser).
- Mengisi data formulir kehadiran (Nama Lengkap, NIP, Unit Kerja, Tanda Tangan Digital, Kabupaten/Kota).
- Mengunggah lampiran jika diminta.
- Membubuhkan tanda tangan digital langsung pada layar sentuh ponsel atau pointer laptop.
- Melihat konfirmasi setelah pengiriman sukses.
- Dilindungi oleh sistem pencegahan pengisian ganda pada pertemuan yang sama.

### Ketentuan otorisasi

- Gunakan middleware, Policies, atau Gates Laravel untuk memeriksa hak akses.
- Periksa kepemilikan/penugasan acara pada setiap operasi yang membaca atau mengubah data acara.
- Jangan mempercayai `user_id`, `event_id`, role, atau identifier sensitif yang dikirim dari browser tanpa verifikasi server.
- Penghapusan atau penonaktifan akun Admin tidak boleh otomatis menghapus acara dan respons yang pernah dikelolanya.

---

## 4. Modul Produk dan Spesifikasi Halaman

### 4.1 Dashboard Admin
Dashboard Admin adalah halaman khusus yang digunakan oleh admin untuk mengontrol, mengelola, dan memantau seluruh aktivitas absensi pertemuan.

#### A. Menu Sidebar
1. **Dashboard:** Menampilkan ringkasan informasi absensi, seperti jumlah peserta, jumlah kehadiran, dan jumlah pertemuan.
2. **Data Pertemuan:** Digunakan untuk menambahkan, mengubah, menghapus, dan melihat informasi kegiatan pertemuan. Misalnya nama kegiatan, tanggal, waktu, dan lokasi.
3. **Data Peserta:** Menampilkan daftar orang tua atau peserta yang terdaftar. Admin dapat melihat dan mengelola identitas peserta.
4. **Rekap Kehadiran:** Menampilkan daftar peserta yang sudah hadir dan belum hadir dalam setiap pertemuan.
5. **Laporan:** Menyediakan laporan kehadiran yang dapat diunduh dalam format PDF atau Excel.
6. **Pengaturan:** Digunakan untuk mengatur informasi akun admin dan kebutuhan sistem.
7. **Logout:** Mengakhiri sesi admin dan keluar dari dashboard.

#### B. Ringkasan Dashboard
Pada halaman utama terdapat beberapa informasi:
- **Total Peserta:** Jumlah seluruh peserta yang terdaftar.
- **Sudah Hadir:** Jumlah peserta yang telah mengisi absensi.
- **Belum Hadir:** Jumlah peserta yang belum mengisi absensi.
- **Total Pertemuan:** Jumlah kegiatan pertemuan yang tersedia.

---

### 4.2 Halaman Absensi Pertemuan
Halaman Absensi Pertemuan merupakan halaman utama yang digunakan oleh orang tua untuk melakukan pengisian daftar kehadiran dalam kegiatan pertemuan sekolah. Halaman ini dirancang dengan tampilan sederhana, bersih, dan mudah dipahami agar orang tua dapat mengisi absensi melalui HP maupun laptop tanpa kesulitan.

Pada bagian atas halaman terdapat judul **Absensi Pertemuan**, identitas kegiatan, serta petunjuk singkat agar pengguna mengetahui tujuan pengisian formulir.

Selanjutnya, terdapat formulir data peserta yang terdiri dari:

* **Nama Lengkap**, untuk memasukkan nama lengkap orang tua atau peserta yang hadir.
* **NIP (Nomor Induk Pegawai)**, untuk memasukkan nomor identitas pegawai.
* **Unit Kerja**, untuk menunjukkan tempat atau instansi peserta bekerja.
* **Tanda Tangan Digital**, sebagai bukti kehadiran peserta yang dapat dibuat langsung melalui layar HP atau menggunakan mouse pada laptop.
* **Kabupaten/Kota**, berupa pilihan dropdown yang dilengkapi fitur pencarian agar pengguna lebih mudah menemukan wilayahnya.
* **Tanggal Kehadiran**, yang terisi secara otomatis sesuai tanggal saat formulir dibuka atau dikirim.
* **Waktu Kehadiran**, yang juga tercatat secara otomatis berdasarkan waktu pengiriman absensi.

Setelah seluruh data dilengkapi, pengguna dapat menekan tombol **Kirim Absensi** untuk mengirimkan data ke sistem. Sebelum disimpan, sistem akan memeriksa kelengkapan formulir agar tidak ada data penting yang terlewat.

Jika absensi berhasil dikirim, akan muncul pemberitahuan bahwa kehadiran telah berhasil dicatat. Data tersebut kemudian tersimpan di database dan dapat dilihat oleh admin melalui Dashboard Admin, khususnya pada bagian Data Absensi dan Rekap Kehadiran.

Untuk menjaga keakuratan data, sistem juga dilengkapi fitur pencegahan pengisian absensi berulang pada pertemuan yang sama. Dengan begitu, satu peserta tidak dapat mengirimkan kehadiran berkali-kali untuk kegiatan yang sama.

Secara keseluruhan, halaman Absensi Pertemuan bertujuan untuk mempermudah orang tua dalam mencatat kehadiran secara digital, mengurangi penggunaan kertas, mempercepat proses pendataan, serta membantu pihak sekolah mengelola informasi kehadiran dengan lebih praktis, rapi, dan terstruktur.

---

### 4.3 Form Login Absensi (📄 Form Login Absensi)

1. **Judul dan keterangan panduan:** Memberi tahu pengguna bahwa halaman ini adalah halaman masuk, dan pengguna wajib memasukkan NIP serta kata sandi untuk membuka halaman absensi.
2. **Kolom NIP (Nomor Induk Pegawai):** Tempat memasukkan nomor identitas pegawai sebagai nama pengguna untuk proses masuk.
3. **Kolom Password:** Tempat memasukkan kata sandi yang sudah terdaftar.
4. **Verifikasi geser:** Fitur keamanan, pengguna harus menggeser tombol untuk membuktikan bukan robot, sebelum bisa menekan tombol login.
5. **Tombol Login:** Tombol utama untuk mengirim data NIP dan kata sandi, lalu masuk ke dalam sistem.
6. **Tautan Lupa password?:** Fitur bantuan, jika pengguna lupa kata sandi, bisa membuka halaman pemulihan kata sandi.
7. **Tombol Beranda (pojok kanan atas):** Fitur untuk kembali ke halaman utama situs.

---

### 4.4 Modul Inti

- Authentication dan authorization (Login Admin & Form Login Absensi).
- Dashboard.
- Event / Meeting Management (Data Pertemuan).
- Participant Management (Data Peserta).
- Custom Form Builder.
- Form Template.
- Public Attendance Form (Halaman Absensi Pertemuan).
- QR Code dan public link.
- Submission Management & Rekap Kehadiran.
- Reporting dan Export (PDF, Excel / XLSX, CSV).
- Audit Log.
- System Settings & Pengaturan Akun.

Fitur seperti conditional logic kompleks, OTP, integrasi WhatsApp, integrasi sistem akademik, dan tanda tangan elektronik tersertifikasi bukan bagian dari kebutuhan minimum awal. Implementasikan hanya sebagai pengembangan terencana.

---

## 5. Aturan Domain dan Data

Pisahkan tiga konsep utama berikut. Jangan menggabungkan acara, struktur formulir, dan respons peserta menjadi satu entitas.

### 5.1 Event

Menyimpan identitas kegiatan, misalnya:

- Nama acara.
- Deskripsi.
- Penyelenggara.
- Lokasi.
- Tanggal dan waktu acara.
- Batas peserta jika digunakan.
- Status acara.
- Admin yang ditugaskan.
- Identifier atau token publik yang aman.

### 5.2 Form dan Form Version

Form menyimpan identitas formulir yang digunakan pada acara. Struktur pertanyaan harus memiliki versi agar perubahan formulir tidak merusak respons historis.

Setiap field minimal memiliki:

- ID stabil atau key unik.
- Label pertanyaan.
- Tipe input.
- Deskripsi/petunjuk opsional.
- Status wajib atau opsional.
- Urutan tampilan.
- Konfigurasi validasi.
- Opsi jawaban untuk field pilihan.

Tipe input minimum yang didukung:

- Short Text.
- Long Text.
- Email.
- Phone.
- Number.
- Dropdown.
- Radio.
- Checkbox.
- Date.
- Time.
- File Upload.
- Signature.
- Heading.
- Description.

Gunakan konfigurasi field yang tervalidasi. Jangan mengeksekusi kode, expression, atau HTML mentah yang dimasukkan pengguna sebagai konfigurasi formulir.

### 5.3 Submission dan Submission Answer

Submission menyimpan satu pengiriman formulir oleh peserta. Jawaban harus terhubung ke identitas field yang stabil, bukan hanya nama/label pertanyaan.

- Simpan snapshot atau referensi versi formulir yang digunakan saat submission dibuat.
- Respons lama harus tetap dapat ditampilkan meskipun formulir kemudian diperbarui.
- Simpan waktu penerimaan berdasarkan waktu server.
- Validasi ulang semua jawaban di backend berdasarkan versi formulir yang aktif untuk pengiriman tersebut.
- Simpan file pada storage privat dan akseskan melalui endpoint yang memeriksa izin.

### Entitas/tabel yang dapat digunakan

Sesuaikan dengan kebutuhan dan konvensi repository, tetapi cakupan data umumnya meliputi:

- `users`
- `events`
- `event_admins` atau tabel pivot penugasan Admin
- `forms`
- `form_versions`
- `form_fields` atau konfigurasi field per versi
- `submissions`
- `submission_answers`
- `submission_files`
- `audit_logs`
- `system_settings`

Jangan membuat semua tabel tersebut secara membabi buta. Periksa kebutuhan relasi, konsistensi, dan struktur yang telah ada sebelum membuat migration.

---

## 6. Status dan Aturan Bisnis

Pisahkan status acara, formulir, dan respons.

### Status acara

- `draft`
- `scheduled`
- `ongoing`
- `completed`
- `archived`

### Status formulir

- `draft`
- `published`
- `closed`

### Status respons

- `valid`
- `flagged`
- `cancelled`

### Aturan bisnis wajib

- Acara draft tidak dapat diakses publik.
- Formulir yang belum diterbitkan tidak menerima respons publik.
- Formulir closed menolak submission baru.
- Acara yang selesai tetap dapat dilihat dan dilaporkan oleh Admin yang berwenang.
- Perubahan formulir yang sudah dipublikasikan harus membuat versi baru atau mekanisme versioning setara.
- Respons lama harus tetap mengacu pada versi formulir saat respons dikirim.
- Penghapusan respons harus dibatasi izinnya dan dicatat dalam audit log.
- Gunakan soft delete bila sesuai kebutuhan retensi dan pemulihan data.
- Jangan menghapus data historis hanya karena akun Admin dihapus.
- Aturan tanggal dan jam harus konsisten menggunakan timezone `Asia/Jakarta` untuk tampilan dan operasional sekolah.
- Timestamp penerimaan kehadiran harus berasal dari server, bukan jam perangkat peserta.

---

## 7. Custom Form Builder

Form Builder adalah salah satu fitur utama DIHADIR.

### Pengalaman admin

Admin harus dapat:

- Menambah pertanyaan.
- Memilih tipe input.
- Mengedit label dan deskripsi.
- Mengatur placeholder.
- Menentukan wajib/opsional.
- Mengatur opsi pilihan.
- Mengatur batas minimal/maksimal yang relevan.
- Mengubah urutan pertanyaan.
- Menonaktifkan pertanyaan jika diperlukan.
- Melihat preview formulir publik.
- Memilih template dan menyesuaikannya.
- Menerbitkan atau menutup formulir.

### Pengalaman peserta

- Formulir harus mobile-first dan nyaman digunakan pada layar kecil.
- Label dan petunjuk harus jelas.
- Field wajib harus ditandai dengan jelas.
- Tampilkan validasi yang dekat dengan field terkait.
- Pertahankan jawaban yang telah diisi ketika validasi gagal, sejauh aman.
- Tampilkan loading state saat submit.
- Cegah klik submit berulang.
- Tampilkan konfirmasi sukses yang jelas.

Conditional logic kompleks dapat ditunda. Jangan menambahkannya sebelum Form Builder dasar, validasi, versioning, dan penyimpanan respons stabil.

---

## 8. QR Code, Link Publik, dan Pencegahan Duplikasi

- Setiap formulir yang diterbitkan memiliki tautan publik dan QR Code.
- Gunakan token/identifier publik yang tidak mudah ditebak; jangan mengekspos primary key berurutan sebagai satu-satunya pengaman.
- Mode akses dasar dapat berupa `Open Attendance` untuk acara umum.
- Sediakan opsi `Verified Attendance` atau kode akses untuk acara terbatas jika diperlukan.
- QR statis hanya membuka formulir dan **tidak membuktikan** peserta benar-benar berada di lokasi acara.
- Jangan menggunakan IP address sebagai satu-satunya identitas peserta atau dasar pencegahan duplikasi.
- Terapkan idempotency atau mekanisme setara untuk mencegah satu request yang dikirim ulang menghasilkan submission ganda.
- Aturan satu respons per peserta harus dapat dikonfigurasi berdasarkan field identitas yang relevan, jika acara membutuhkannya.
- Jelaskan kepada Admin bahwa pembatasan berdasarkan NIP/NIS/email hanya sekuat validasi identitas yang diterapkan.

---

## 9. Pelaporan dan Ekspor

Format minimum:

- PDF.
- XLSX.
- CSV.
- Print-friendly page.

Laporan minimal berisi:

- Identitas dan judul acara.
- Penyelenggara, tanggal, dan lokasi.
- Waktu laporan dibuat.
- Tabel respons peserta.
- Ringkasan jumlah respons.
- Kolom tanda tangan jika formulir menggunakannya.

Karena field formulir dinamis, proses ekspor harus mengambil kolom berdasarkan versi formulir dan jawaban yang benar. Jangan mengandalkan daftar kolom peserta yang di-hardcode.

Lindungi ekspor CSV/XLSX dari formula injection. Pastikan ekspor hanya dapat dilakukan oleh Admin yang memiliki akses ke acara terkait atau Super Admin.

---

## 10. Audit, Keamanan, dan Privasi

Keamanan harus diterapkan di backend dan diuji. Menyembunyikan elemen UI bukan kontrol keamanan.

### Persyaratan minimum

- Gunakan autentikasi Laravel yang sesuai dengan kebutuhan project.
- Simpan password menggunakan mekanisme hashing bawaan Laravel.
- Gunakan CSRF protection pada form/session yang relevan.
- Validasi request menggunakan Form Request atau pola validasi terpusat.
- Gunakan Eloquent/query binding; hindari raw SQL yang menggabungkan input pengguna.
- Terapkan authorization Policies/Gates pada operasi sensitif.
- Escape output; sanitasi input rich text bila fitur tersebut benar-benar diperlukan.
- Terapkan rate limiting pada login, endpoint publik, dan submission.
- Validasi ukuran, MIME type, ekstensi, dan isi file upload sejauh relevan.
- Simpan file sensitif di private storage, bukan direktori publik tanpa kontrol.
- Jangan mencatat password, token rahasia, tanda tangan, atau data pribadi berlebihan ke log.
- Jangan menaruh secret, credential, atau API key di source code.
- Gunakan HTTPS pada deployment produksi.
- Sediakan backup database dan prosedur pemulihan yang pernah diuji.

### Audit log

Catat aktivitas penting, minimal:

- Pembuatan dan perubahan acara.
- Perubahan/penerbitan formulir.
- Perubahan status acara/formulir.
- Pengeditan atau pembatalan respons.
- Penghapusan data.
- Ekspor laporan.
- Perubahan akun dan hak akses.

Audit log harus menyimpan aktor, aksi, objek terkait, waktu, dan metadata minimum yang relevan. Admin biasa tidak boleh dapat mengubah atau menghapus audit log.

### Privasi data

- Minta hanya data yang dibutuhkan penyelenggara.
- Tampilkan pemberitahuan privasi pada formulir publik.
- Batasi akses ke NIP, NIS, nomor telepon, tanda tangan, dan lampiran.
- Tentukan kebijakan retensi dan penghapusan data bersama pengelola sekolah.
- Sediakan prosedur koreksi data.
- Selaraskan pengolahan data dengan ketentuan perlindungan data pribadi yang berlaku di Indonesia.

---

## 11. Design System — Slate & Blue, Minimalist Corporate

Gunakan palet berikut sebagai standar visual seluruh aplikasi DIHADIR. Jangan membuat palet baru untuk halaman tertentu tanpa alasan yang kuat.

### Design tokens

| Token | Hex | Penggunaan |
|---|---|---|
| `primary` | `#334155` | Struktur utama, sidebar, elemen navigasi |
| `accent` | `#3B82F6` | Aksi utama, link, tombol submit, fokus input |
| `background` | `#F8FAFC` | Latar halaman |
| `surface` | `#FFFFFF` | Card, modal, navbar, formulir |
| `text` | `#0F172A` | Heading dan teks utama |
| `border` | `#CBD5E1` | Border, divider, pemisah |
| `success` | `#16A34A` | Berhasil/valid |
| `warning` | `#F59E0B` | Peringatan/perlu pemeriksaan |
| `error` | `#DC2626` | Kesalahan/gagal |
| `info` | `#0EA5E9` | Informasi |

### Aset Logo & Identitas Brand Resmi

Seluruh antarmuka aplikasi DIHADIR wajib menggunakan aset logo resmi yang telah tersimpan di direktori `public/images/`:

| Aset Berkas | Visual & Deskripsi | Penempatan Wajib |
|---|---|---|
| `public/images/logo-dihadir-icon.png` | Monogram 'D' dengan ikon clipboard & centang Slate-700 & Blue-500 | Favicon browser, ikon header mobile, penanda sistem, avatar mini. |
| `public/images/logo-dihadir-full.png` | Logo horizontal lengkap (Monogram + divider + teks "DIHADIR") | Header Sidebar Dashboard Admin, Form Login Admin, Form Login Absensi, navbar utama. |
| `public/images/logo-smkn1majalengka.png` | Lambang resmi SMKN 1 Majalengka (Perisai hitam, sayap kuning, bintang merah, bola dunia, buku) | KOP surat laporan resmi (PDF & Cetak), header Form Login Absensi, header formulir kehadiran publik orang tua. |

> [!IMPORTANT]
> Dilarang mengganti logo resmi dengan placeholder teks biasa atau ikon generik acak ketika komponen antarmuka membutuhkan logo. Pastikan tag `<img>` selalu mengarah ke aset di atas (`asset('images/logo-dihadir-full.png')`, dsb.).

### Penerapan warna

| Komponen | Aturan |
|---|---|
| Navbar | `#FFFFFF` |
| Sidebar | `#334155` atau varian surface putih sesuai layout |
| Halaman utama | `#F8FAFC` |
| Form card | `#FFFFFF` |
| Input field | `#FFFFFF` dengan border `#CBD5E1` |
| Input focus | Accent `#3B82F6` dengan focus ring yang terlihat |
| Tombol utama/submit | `#3B82F6` dengan teks kontras |
| Heading | `#0F172A` |
| Deskripsi/teks sekunder | Slate netral, misalnya `#64748B` |
| Status | Gunakan success, warning, error, dan info sesuai makna |

### Typography

- Gunakan **Inter** untuk seluruh antarmuka.
- Terapkan skala tipografi yang konsisten untuk heading, body, label, helper text, dan tabel.
- Hindari penggunaan terlalu banyak ukuran atau ketebalan font.
- Pastikan teks tetap terbaca pada layar ponsel.

### Karakter visual

- Minimalist Corporate.
- Clean layout.
- Subtle shadow; hindari bayangan berat.
- Rounded corners yang konsisten, umumnya 8–12 px.
- Generous spacing dan alignment yang rapi.
- Border tipis dan pemisah yang tidak mencolok.
- Gunakan ikon secara konsisten; Lucide dapat digunakan bila sesuai dengan dependency project.
- Hindari gradient dekoratif, warna neon, efek berlebihan, dan animasi yang tidak membantu tugas pengguna.

### Konsistensi desain

- Buat design tokens/CSS variables agar warna tidak tersebar sebagai hardcode di seluruh komponen.
- Buat komponen bersama untuk button, input, select, textarea, badge/status, card, modal, table, empty state, dan feedback.
- Semua halaman dashboard, Form Builder, formulir publik, tabel kehadiran, dan laporan harus menggunakan sistem visual yang sama.
- Status tidak boleh dibedakan hanya dengan warna; sertakan label atau ikon yang jelas.
- Pastikan kontras teks dan kontrol memadai serta mendukung navigasi keyboard.

---

## 12. Pedoman UI/UX

- Utamakan mobile-first untuk formulir publik.
- Dashboard harus responsif pada desktop, tablet, dan ponsel.
- Pertahankan navigasi ringkas dan istilah menu yang mudah dipahami dalam Bahasa Indonesia.
- Gunakan label yang spesifik, bukan placeholder sebagai pengganti label.
- Sediakan loading, success, error, empty, dan disabled states.
- Berikan konfirmasi sebelum tindakan destruktif.
- Tampilkan pesan validasi yang membantu pengguna memperbaiki kesalahan.
- Gunakan pagination atau server-side filtering untuk daftar respons yang besar.
- Tabel harus tetap dapat digunakan pada layar kecil, misalnya melalui horizontal scroll atau tampilan alternatif.
- Jangan mengorbankan keterbacaan demi membuat dashboard terlihat padat.
- Jangan menampilkan data contoh atau angka ilustrasi seolah-olah merupakan data produksi.

---

## 13. Struktur Kode dan Konvensi Laravel

### Standar Routing & Clean URLs (Tanpa Ekstensi `.php`)
- **DILARANG KERAS memunculkan ekstensi `.php` atau segmen `index.php` pada URL browser.**
- Seluruh URL aplikasi wajib berupa URL bersih (Pretty/RESTful URL), misalnya:
  - `/login` (bukan `/login.php`)
  - `/admin/dashboard` (bukan `/admin/dashboard.php` atau `/index.php/admin/dashboard`)
  - `/admin/pertemuan` (bukan `/admin/meetings.php`)
  - `/absensi/login` (bukan `/absensi/login.php`)
  - `/p/{token}` (bukan `/p.php?token=...`)
- Seluruh routing wajib didefinisikan menggunakan Laravel Named Routes di `routes/web.php` dan dipanggil via helper `route('nama.route')` pada Blade template dan redirect response.
- Web server rewrite engine (Apache `.htaccess` bawaan Laravel atau Nginx `try_files $uri $uri/ /index.php?$query_string;`) harus aktif agar setiap request diarahkan secara transparan ke Front Controller tanpa mengubah URL yang terlihat oleh pengguna.

### Konvensi Arsitektur & Kode

- Letakkan business logic yang kompleks pada Actions/Services atau kelas domain yang terorganisasi; hindari controller yang terlalu besar.
- Controller menangani HTTP request/response dan mengorkestrasi alur, bukan menampung seluruh business logic.
- Gunakan Form Request untuk validasi request yang kompleks atau digunakan berulang.
- Gunakan Policies/Gates untuk authorization.
- Gunakan Eloquent relationships secara jelas dan hindari N+1 query dengan eager loading.
- Gunakan database transactions untuk operasi yang harus atomik, terutama pembuatan submission beserta jawaban dan file metadata.
- Gunakan queue untuk pekerjaan berat seperti ekspor besar atau pengiriman notifikasi bila diperlukan.
- Gunakan named routes dan route model binding dengan authorization yang tepat.
- Pisahkan komponen Blade agar dapat digunakan ulang.
- Jangan menaruh query database, validasi sensitif, atau aturan hak akses hanya di frontend.
- Gunakan migration yang dapat dijalankan ulang sesuai praktik Laravel; jangan mengubah migration lama yang sudah dipakai bersama tanpa memahami dampaknya.
- Tambahkan index dan constraint database untuk relasi serta aturan unik yang memang dibutuhkan.
- Hindari overengineering dan abstraksi yang tidak memberi manfaat nyata.

---

## 14. Testing dan Definition of Done

Setiap fitur harus memiliki pengujian yang sebanding dengan risiko dan kompleksitasnya.

### Pengujian minimum

- Authentication dan akses berdasarkan role.
- Admin tidak dapat mengakses acara milik Admin lain.
- Pembuatan, pengeditan, publikasi, penutupan, dan duplikasi acara.
- Pembuatan serta validasi setiap tipe field yang didukung.
- Versioning formulir dan pembacaan respons historis.
- Submission publik dan timestamp server.
- Pencegahan request submission ganda.
- Batas periode pengisian dan formulir yang ditutup.
- Upload file dan akses file privat.
- Ekspor PDF, XLSX, dan CSV.
- Audit log untuk tindakan penting.
- Responsive layout dan aksesibilitas dasar.

### Definition of Done

Sebuah fitur dianggap selesai apabila:

1. Alur normal berjalan sesuai kebutuhan.
2. Validasi dan error state ditangani.
3. Authorization diperiksa di backend.
4. UI mengikuti design system Slate & Blue.
5. Tampilan responsif dan dapat digunakan melalui keyboard bila relevan.
6. Test yang relevan lulus.
7. Tidak ada secret atau data sensitif di source code/log.
8. Dokumentasi diperbarui bila ada perubahan konfigurasi, API, atau alur operasional.

---

## 15. Instruksi Kerja untuk AI Coding Agent

> [!CAUTION]
> **ATURAN MUTLAK: ZERO DEMO / ZERO DUMMY DATA — LANGSUNG SIAP PRODUKSI**
> 1. **DILARANG KERAS membuat data dummy, mock data, atau generator data fiktif** (seperti Faker untuk database seeder, pertemuan contoh, peserta palsu, atau absensi tiruan) di kode maupun database.
> 2. **Database harus dalam kondisi bersih (clean state).** Seeder yang diizinkan HANYALAH pembuatan 1 akun Super Admin awal resmi (menggunakan kredensial aman dari `.env` atau command) dan data referensi statis sistem (seperti daftar baku Kabupaten/Kota).
> 3. **Terapkan Empty State yang elegan.** Jika belum ada data pertemuan, peserta, atau absensi riil, tampilan antarmuka (Dashboard, Tabel, Rekap, Laporan) WAJIB menampilkan komponen *Empty State* yang bersih dan profesional dengan ajakan bertindak (*call-to-action*) nyata, BUKAN diisi data palsu.
> 4. **Semua angka metrik dashboard wajib riil.** Angka Total Peserta, Sudah Hadir, Belum Hadir, dan Total Pertemuan harus berasal dari hasil agregasi query database nyata (menampilkan angka `0` jika sistem baru pertama kali dipasang).
> 5. **Seluruh fungsi harus siap pakai.** Semua tombol, form input, validasi, canvas tanda tangan, verifikasi geser, dan ekspor laporan harus berfungsi penuh langsung untuk operasional sekolah tanpa ada *mock logic* atau *hardcoded placeholder return*.

Saat mengerjakan repository DIHADIR:

1. **Periksa sebelum mengubah.** Baca struktur project, file konfigurasi, dependency, pola kode, dan test yang sudah ada.
2. **Pahami konteks.** Cari model, migration, route, controller, middleware, view, dan komponen terkait sebelum membuat implementasi baru.
3. **Jaga kompatibilitas.** Gunakan versi Laravel dan package yang sudah terpasang; jangan melakukan upgrade besar tanpa alasan dan persetujuan.
4. **Kerjakan sesuai scope.** Jangan menambahkan fitur, role, package, atau service eksternal yang tidak diminta.
5. **Pertahankan tiga role maksimum.** Role yang berlaku hanya Super Admin, Admin, dan Peserta.
6. **Utamakan keamanan.** Validasi, authorization, dan pemeriksaan kepemilikan data wajib dilakukan di server.
7. **Jaga data historis.** Jangan mengubah atau menghapus makna respons lama ketika formulir diperbarui.
8. **Ikuti design system.** Gunakan token Slate & Blue dan font Inter secara konsisten.
9. **Zero Demo Data & Hindari placeholder produksi.** Jangan membuat data contoh, tombol yang tidak berfungsi, angka statistik palsu, atau aksi yang hanya mengubah tampilan tanpa menyimpan data.
10. **Jangan merusak data.** Jangan menjalankan destructive migration, menghapus tabel, atau menghapus data tanpa memahami dampak dan memperoleh persetujuan yang diperlukan.
11. **Uji perubahan.** Jalankan test, formatter, linter, atau pemeriksaan yang tersedia dan relevan.
12. **Laporkan hasil secara jelas.** Sebutkan file penting yang diubah, keputusan teknis, test yang dijalankan, dan hal yang belum selesai.

Jika instruksi user bertentangan dengan dokumen ini, ikuti instruksi user yang terbaru untuk scope yang diminta, tetapi jelaskan dampak penting terhadap keamanan, kompatibilitas, atau data. Jangan diam-diam mengubah keputusan arsitektur inti.

---

## 16. Prioritas Pengembangan

Urutan pengembangan yang disarankan:

1. Foundation: setup Laravel, database, authentication, role, authorization.
2. Event Management: CRUD acara, penugasan Admin, status, duplikasi.
3. Form Builder: field, validasi, template, preview, versioning.
4. Attendance: halaman publik, QR Code, submission, timestamp, anti-duplicate.
5. Data & Reporting: manajemen respons, filter, PDF/XLSX/CSV, audit log.
6. Production Readiness: security review, backup/restore test, responsive test, load test, deployment, dan dokumentasi.

Fitur lanjutan seperti conditional logic kompleks, QR individual, OTP, WhatsApp, dan integrasi eksternal dikerjakan setelah alur inti stabil.

---

**Identitas produk:** DIHADIR  
**Pengelola:** Tim IT Support Perpus SMKN 1 Majalengka  
**Design system:** Slate & Blue — Minimalist Corporate  
**Bahasa antarmuka utama:** Bahasa Indonesia


<!-- antislop:start -->
## Antislop & UI Craftsmanship Standards (Auto-Active)
antislop active: during (global preference).

Untuk setiap pekerjaan UI, copy, mobile responsive, styling, dan kode di project DIHADIR, otomatis terapkan keahlian berikut:
- **Core Rules**: `.agents/skills/antislop/SKILL.md`
- **UI / Visual**: `.agents/skills/antislop-ui/SKILL.md`
- **Mobile Responsive**: `.agents/skills/antislop-layoutmobile/SKILL.md`
- **People & Accessibility**: `.agents/skills/antislop-human/SKILL.md`
- **Copywriting**: `.agents/skills/antislop-copywriting/SKILL.md`
- **Code Comments**: `.agents/skills/antislop-code/SKILL.md`

### Prinsip Utama yang Wajib Diterapkan:
1. **Bebas AI Slop**: Dilarang menggunakan gradien biru-ke-ungu generik tanpa identitas brand, glassmorphism/shadow/glow berlebih di seluruh komponen, dan bentuk kapsul (pill) yang monoton.
2. **Design System Konsisten**: Mengikuti token Slate & Blue (`primary`: `#334155`, `accent`: `#3B82F6`) dan tipografi **Inter** sesuai Bagian 11.
3. **Mobile-First & Responsif (R-03)**: Bebas horizontal scroll/overflow pada layar ponsel, container teks fleksibel, dan tap target tombol minimal 44px.
4. **Kontrol Interaktif Berfungsi Penuh (R-26)**: Setiap tombol, modal, link navigasi, dan form harus memiliki aksi nyata. Tidak diperkenankan membuat kontrol mati (dead controls).
5. **Kejujuran Data & Konten (R-17, R-18, R-38)**: Dilarang membuat statistik fiktif atau testimoni palsu. Gunakan label placeholder yang jujur jika data belum tersedia.
6. **Kelengkapan State UI (R-27)**: Setiap tampilan data wajib memiliki *empty state*, *loading state*, dan *error state*.
7. **Aksesibilitas (R-25, R-32)**: Kontras rasio teks minimal 4.5:1 (WCAG AA), navigasi keyboard (`Tab`, `Shift+Tab`, `Enter`, `Esc`) berfungsi, dan focus ring selalu terlihat.
<!-- antislop:end -->


---

## 17. Aturan Wajib untuk Agent di Masa Depan (Future Agent Governance)

Setiap AI coding agent yang bekerja pada project DIHADIR di masa depan **WAJIB MENGIKUTI 4 PILAR UTAMA BERIKUT**:

1. **Patuhi Dokumen Roadmap & Fase di [development/](file:///D:/DIHADIR/development):**
   - Wajib membaca [00-ROADMAP.md](file:///D:/DIHADIR/development/00-ROADMAP.md) sebelum memulai pekerjaan.
   - Kerjakan implementasi secara berurutan sesuai fase: [PHASE-01](file:///D:/DIHADIR/development/PHASE-01-FOUNDATION-AND-AUTH.md) sampai [PHASE-06](file:///D:/DIHADIR/development/PHASE-06-SETTINGS-SECURITY-AND-DEPLOYMENT.md).
   - Patuhi aturan **ZERO DEMO DATA**: Dilarang membuat dummy/mock data atau seeder palsu. Terapkan *Empty State* yang elegan pada seluruh view.
2. **Patuhi Dokumen [AGENTS.md](file:///D:/DIHADIR/AGENTS.md):**
   - Batasan maksimal 3 role (Super Admin, Admin, Peserta).
   - Otorisasi ketat di backend (Gates/Policies), bukan hanya menyembunyikan tombol UI.
   - Timezone wajib `Asia/Jakarta`, timestamp kehadiran berasal murni dari server.
   - Design system konsisten menggunakan token Slate & Blue (`#334155`, `#3B82F6`) dan font Inter.
3. **Patuhi Dokumen [README.md](file:///D:/DIHADIR/README.md):**
   - Pahami identitas resmi produk, arsitektur, dan kepemilikan/pengembang aplikasi.
4. **Terapkan Seluruh Skills di [.agents/skills/](file:///D:/DIHADIR/.agents/skills/):**
   - Otomatis terapkan keahlian `antislop`, `antislop-ui`, `antislop-layoutmobile`, `antislop-human`, `antislop-code`, `antislop-copywriting`, `frontend-design`, dan `ui-taste`.
   - Jalankan verifikasi *build* (`npm run build`, `php artisan test`) sebelum menyatakan sebuah tugas selesai.
