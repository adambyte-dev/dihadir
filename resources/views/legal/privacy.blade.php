<x-layouts.landing
    title="Kebijakan Privasi & Perlindungan Data Peserta"
    description="Kebijakan Privasi aplikasi DIHADIR SMKN 1 Majalengka sesuai standar UU No. 27 Tahun 2022 (UU PDP). Menjelaskan komitmen perlindungan data dan keamanan presensi peserta."
    :canonical="route('legal.privacy')"
>
    @push('head')
    @php
        $breadcrumbData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Beranda',
                    'item' => route('landing'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Kebijakan Privasi',
                    'item' => route('legal.privacy'),
                ],
            ],
        ];
    @endphp
    <script type="application/ld+json">
    {!! json_encode($breadcrumbData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endpush
    <div class="bg-white border-b border-slate-200/80 py-12 sm:py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="border-b border-slate-200 pb-8 space-y-3">
                <div class="inline-flex items-center gap-2 rounded-lg border border-blue-200 bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <span>Standar Kepatuhan UU No. 27 Tahun 2022 (UU PDP)</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Kebijakan Privasi & Perlindungan Data Peserta
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Terakhir diperbarui: 14 November 2026 · Berlaku untuk seluruh pengguna aplikasi <strong>DIHADIR</strong> di SMK Negeri 1 Majalengka.
                </p>
            </div>

            {{-- Isi Dokumen --}}
            <div class="mt-8 space-y-8 text-xs sm:text-sm leading-relaxed text-slate-700">
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">1. Komitmen Perlindungan Privasi</h2>
                    <p>
                        Pemerintah SMK Negeri 1 Majalengka melalui platform <strong>DIHADIR (Digital Attendance Management System)</strong> berkomitmen penuh untuk melindungi kerahasiaan dan keamanan data pribadi setiap peserta kegiatan, meliputi bapak/ibu orang tua siswa, guru, tenaga kependidikan, pengurus komite, serta mitra perwakilan Dunia Usaha dan Dunia Industri (DUDI).
                    </p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">2. Data Pribadi yang Kami Kumpulkan</h2>
                    <p>Ketika Anda mengisi formulir presensi pertemuan atau mendaftar kehadiran, sistem kami mengumpulkan informasi terbatas yang relevan dengan administrasi kegiatan sekolah:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li><strong>Data Pokok Identitas:</strong> Nama Lengkap beserta gelar resmi, Nomor Induk Pegawai (NIP/NUPTK/NIK), Unit Kerja atau instansi asal, dan Kabupaten/Kota domisili kerja.</li>
                        <li><strong>Bukti Kehadiran Digital:</strong> Goresan tanda tangan digital (digital signature canvas) yang tersimpan dalam format terkompresi.</li>
                        <li><strong>Metadata Kedatangan Otomatis:</strong> Catatan waktu server WIB (Asia/Jakarta), status verifikasi geser, serta identifier teknis sesi untuk pencegahan pengisian ganda (idempotency key).</li>
                        <li><strong>Jawaban Pertanyaan Panitia Khusus:</strong> Informasi tambahan yang secara spesifik dirumuskan panitia acara melalui form builder (misal: nama putra/putri, rombongan belajar, atau berkas surat penugasan).</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">3. Tujuan Penggunaan Data</h2>
                    <p>Seluruh data yang tercatat hanya digunakan untuk keperluan sah kedinasan dan operasional sekolah:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Penyusunan Berita Acara Rapat dan Daftar Hadir Fisik/Digital resmi sekolah.</li>
                        <li>Lampiran kelengkapan Surat Pertanggungjawaban (SPJ) kegiatan serta audit BOS/BOPD.</li>
                        <li>Pencegahan maladministrasi dan manipulasi data kehadiran oleh pihak yang tidak berhak.</li>
                        <li>Evaluasi tingkat partisipasi pemangku kepentingan dalam program-program sekolah.</li>
                    </ul>
                    <div class="rounded-xl border border-blue-200 bg-blue-50/70 p-4 text-xs text-blue-900">
                        <strong>Jaminan Integritas:</strong> Kami tidak pernah menjual, menyewakan, atau mendistribusikan data pribadi peserta kepada pihak ketiga untuk kepentingan komersial, periklanan, maupun pemasaran apa pun.
                    </div>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">4. Keamanan dan Penyimpanan Data</h2>
                    <p>
                        Kami menerapkan standar teknis dan organisasional yang ketat untuk mengamankan data Anda:
                    </p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Komunikasi terenkripsi penuh menggunakan protokol Transport Layer Security (HTTPS/SSL).</li>
                        <li>Akses terhadap panel rekapitulasi dibatasi ketat melalui mekanisme Role-Based Access Control (hanya Super Admin dan Admin acara yang berwenang).</li>
                        <li>Setiap aktivitas pembuatan, modifikasi, ekspor, dan penghapusan data dicatat secara permanen pada <em>Audit Log</em> sistem yang tidak dapat diedit oleh admin biasa.</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">5. Retensi dan Hak Subjek Data</h2>
                    <p>
                        Data kehadiran disimpan selama masa retensi administrasi dan kearsipan sekolah yang berlaku di lingkungan Dinas Pendidikan Provinsi Jawa Barat. Sebagai subjek data, Anda berhak mengajukan klarifikasi atau koreksi apabila terdapat kekeliruan dalam pencatatan nama atau instansi melalui panitia penyelenggara acara terkait.
                    </p>
                </section>

                <section class="space-y-3 border-t border-slate-200 pt-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">6. Kontak Pengelola Data</h2>
                    <p>
                        Apabila Anda memiliki pertanyaan, keluhan, atau permintaan terkait pengelolaan data pribadi Anda pada sistem DIHADIR, silakan hubungi:
                    </p>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 space-y-1 text-xs text-slate-700">
                        <p class="font-bold text-slate-900">Unit Pengelola Sistem IT & Informasi Publik</p>
                        <p>SMK Negeri 1 Majalengka</p>
                        <p>Jl. Raya Tonjong–Pinangraja No. 55, Majalengka, Jawa Barat 45413</p>
                        <p>Surel Resmi: <a href="mailto:info@smkn1majalengka.sch.id" class="text-blue-600 hover:underline">info@smkn1majalengka.sch.id</a> · Telp: (0233) 282913</p>
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.landing>
