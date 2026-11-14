<x-layouts.landing
    title="Syarat dan Ketentuan Layanan"
    description="Syarat dan ketentuan layanan DIHADIR SMKN 1 Majalengka. Mengatur hak, kewajiban peserta, kejujuran data presensi, serta tanggung jawab panitia pengelola kegiatan."
    :canonical="route('legal.terms')"
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
                    'name' => 'Syarat & Ketentuan',
                    'item' => route('legal.terms'),
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
                <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700">
                    <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ketentuan Operasional & Kode Etik Penggunaan</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                    Syarat & Ketentuan Layanan DIHADIR
                </h1>
                <p class="text-xs sm:text-sm text-slate-500">
                    Terakhir diperbarui: 14 November 2026 · Mengatur hak dan tanggung jawab seluruh pengguna sistem.
                </p>
            </div>

            {{-- Isi Dokumen --}}
            <div class="mt-8 space-y-8 text-xs sm:text-sm leading-relaxed text-slate-700">
                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">1. Penerimaan Ketentuan</h2>
                    <p>
                        Dengan mengakses situs web <strong>DIHADIR</strong>, memindai QR Code kehadiran, atau mengirimkan data presensi melalui formulir online, Anda menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Jika Anda tidak menyetujui ketentuan ini, Anda dipersilakan mengonfirmasikan mekanisme kehadiran manual kepada panitia pelaksana kegiatan.
                    </p>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">2. Keabsahan Data dan Kejujuran Peserta</h2>
                    <p>Setiap peserta yang mengisi formulir kehadiran berkewajiban untuk:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Mengisikan identitas asli (Nama, NIP/Identitas resmi, dan Instansi) sesuai fakta sebenarnya.</li>
                        <li>Membubuhkan tanda tangan digital pribadi yang sah langsung dari instrumen layar sendiri.</li>
                        <li>Tidak melakukan penitipan absensi (menitipkan kehadiran) atas nama orang lain tanpa surat mandat penugasan resmi.</li>
                        <li>Tidak mencoba mengelabui batas waktu pengisian maupun sistem pencegahan presensi ganda.</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">3. Tanggung Jawab Pengelola Acara (Admin)</h2>
                    <p>Admin dan panitia kegiatan yang memiliki hak kelola atas acara wajib:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Memastikan informasi kegiatan (nama acara, tanggal, waktu pembukaan dan penutupan form) telah akurat sebelum tautan dan QR Code dipublikasikan.</li>
                        <li>Menjaga kerahasiaan akun pengelola dan tidak membagikan kredensial login kepada pihak di luar tim kepanitiaan.</li>
                        <li>Memeriksa keabsahan rekap kehadiran sebelum mencetak atau mengesahkan dokumen pertanggungjawaban kegiatan.</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">4. Larangan Penggunaan (Prohibited Conduct)</h2>
                    <p>Pengguna dilarang keras melakukan tindakan berikut:</p>
                    <ul class="list-disc pl-5 space-y-1.5 text-slate-600">
                        <li>Melakukan serangan Denial-of-Service (DoS), injeksi script berbahaya, atau brute-force terhadap sistem dan basis data presensi.</li>
                        <li>Memanipulasi berkas PDF atau CSV laporan yang telah diterbitkan sistem dengan tujuan rekayasa dokumen.</li>
                        <li>Menyalahgunakan tautan publik form presensi untuk kegiatan di luar izin tertulis sekolah.</li>
                    </ul>
                </section>

                <section class="space-y-3">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">5. Kepemilikan Hak Cipta & Kekayaan Intelektual</h2>
                    <p>
                        Seluruh desain antarmuka, logo DIHADIR, logo SMKN 1 Majalengka, struktur form builder, arsitektur perangkat lunak, serta dokumentasi sistem merupakan karya dan hak milik intelektual SMKN 1 Majalengka bersama tim pengembang.
                    </p>
                    <p>
                        Sekolah atau instansi lain yang berkeinginan mereplikasi, mengadopsi, atau menggunakan sistem ini dipersilakan mengajukan permohonan kemitraan resmi melalui program kerja sama sekolah sebagaimana tercantum pada halaman utama.
                    </p>
                </section>

                <section class="space-y-3 border-t border-slate-200 pt-6">
                    <h2 class="text-base sm:text-lg font-bold text-slate-900">6. Perubahan Ketentuan</h2>
                    <p>
                        SMKN 1 Majalengka berhak memperbarui ketentuan ini sewaktu-waktu sesuai perkembangan kebutuhan regulasi pendidikan nasional. Pembaruan akan berlaku seketika setelah diunggah pada halaman ini.
                    </p>
                </section>
            </div>
        </div>
    </div>
</x-layouts.landing>
