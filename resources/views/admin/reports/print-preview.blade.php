<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak Laporan Kehadiran - {{ $meeting->title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-dihadir-icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .print-container {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm 1.5cm 1.5cm 1.5cm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased py-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a 
            href="{{ route('admin.laporan.index') }}" 
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3 py-2 rounded-lg border border-slate-300 shadow-2xs transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Panel Laporan</span>
        </a>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.laporan.pdf', $meeting) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 transition shadow-2xs"
            >
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh PDF</span>
            </a>

            <button 
                type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Lembar Kehadiran</span>
            </button>
        </div>
    </div>

    <!-- Halaman Dokumen A4 -->
    <div class="print-container max-w-4xl mx-auto bg-white p-10 rounded-xl border border-slate-200 shadow-sm text-slate-900">
        
        <!-- KOP Surat Resmi SMKN 1 Majalengka -->
        <div class="flex items-center gap-4 pb-2">
            <img src="{{ asset('images/logo-smkn1majalengka.png') }}" alt="Lambang SMKN 1 Majalengka" class="h-20 w-auto shrink-0">
            <div class="text-center flex-1 pr-14">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-800 leading-tight">Pemerintah Daerah Provinsi Jawa Barat</h3>
                <h2 class="text-base font-bold uppercase text-slate-900 leading-tight">Dinas Pendidikan, Cabang Dinas Wilayah IX</h2>
                <h1 class="text-xl font-extrabold uppercase tracking-wide text-slate-950 my-0.5">{{ $settings['school_name'] ?? 'SMK Negeri 1 Majalengka' }}</h1>
                <p class="text-[11px] text-slate-600 leading-tight">
                    {{ $settings['school_address'] ?? 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401' }}
                    @if(!empty($settings['school_phone'])) Telp. {{ $settings['school_phone'] }} @endif<br>
                    Website: www.smkn1majalengka.sch.id &bull; E-mail: {{ $settings['school_email'] ?? 'info@smkn1majalengka.sch.id' }}
                </p>
            </div>
        </div>

        <!-- Garis Pemisah Ganda KOP Surat -->
        <div class="border-t-[2.5px] border-slate-950 mt-1 mb-[2px]"></div>
        <div class="border-t border-slate-950 mb-6"></div>

        <!-- Judul Laporan -->
        <div class="text-center mb-6">
            <h2 class="text-base font-bold uppercase tracking-wider text-slate-900">Daftar Hadir Kegiatan Pertemuan</h2>
        </div>

        <!-- Metadata Kegiatan -->
        <div class="mb-6 bg-slate-50 p-4 rounded-lg border border-slate-200/80 text-xs">
            <table class="w-full">
                <tr class="leading-relaxed">
                    <td class="w-36 font-semibold text-slate-700 py-0.5">Nama Kegiatan</td>
                    <td class="w-4 text-center">:</td>
                    <td class="font-bold text-slate-900">{{ $meeting->title }}</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-semibold text-slate-700 py-0.5">Penyelenggara</td>
                    <td class="text-center">:</td>
                    <td class="text-slate-800">{{ $meeting->organizer }}</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-semibold text-slate-700 py-0.5">Hari / Tanggal</td>
                    <td class="text-center">:</td>
                    <td class="text-slate-800">{{ $meeting->meeting_date?->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-semibold text-slate-700 py-0.5">Waktu</td>
                    <td class="text-center">:</td>
                    <td class="text-slate-800">{{ substr($meeting->start_time, 0, 5) }} - {{ substr($meeting->end_time, 0, 5) }} WIB</td>
                </tr>
                <tr class="leading-relaxed">
                    <td class="font-semibold text-slate-700 py-0.5">Tempat / Ruangan</td>
                    <td class="text-center">:</td>
                    <td class="text-slate-800">{{ $meeting->location }}</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Kehadiran -->
        <table class="w-full text-left text-xs border-collapse border border-slate-300 mb-8">
            <thead>
                <tr class="bg-slate-100 border-b border-slate-300 text-slate-800 uppercase font-semibold text-[11px]">
                    <th class="border border-slate-300 py-2.5 px-3 w-10 text-center">No</th>
                    <th class="border border-slate-300 py-2.5 px-3 w-28">NIP</th>
                    <th class="border border-slate-300 py-2.5 px-3">Nama Lengkap</th>
                    <th class="border border-slate-300 py-2.5 px-3 w-36">Unit Kerja</th>
                    <th class="border border-slate-300 py-2.5 px-3 w-24">Kab./Kota</th>
                    @if(isset($customFields) && $customFields->isNotEmpty())
                        @foreach($customFields as $cField)
                            <th class="border border-slate-300 py-2.5 px-3">{{ $cField->label }}</th>
                        @endforeach
                    @endif
                    <th class="border border-slate-300 py-2.5 px-3 w-16 text-center">Waktu</th>
                    <th class="border border-slate-300 py-2.5 px-3 w-24 text-center">Tanda Tangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($attendances as $idx => $att)
                    @php
                        $answersMap = $att->customAnswers->keyBy('meeting_form_field_id');
                    @endphp
                    <tr>
                        <td class="border border-slate-300 py-2 px-3 text-center text-slate-500 font-mono">{{ $idx + 1 }}</td>
                        <td class="border border-slate-300 py-2 px-3 font-mono text-slate-700">{{ $att->nip }}</td>
                        <td class="border border-slate-300 py-2 px-3 font-bold text-slate-900">{{ $att->name }}</td>
                        <td class="border border-slate-300 py-2 px-3 text-slate-700">{{ $att->unit_kerja }}</td>
                        <td class="border border-slate-300 py-2 px-3 text-slate-600">{{ $att->kabupaten_kota ?? '-' }}</td>
                        @if(isset($customFields) && $customFields->isNotEmpty())
                            @foreach($customFields as $cField)
                                @php
                                    $ans = $answersMap->get($cField->id);
                                    $ansText = $ans ? ($ans->answer_value ?? ($ans->file_path ? '[Lampiran]' : '-')) : '-';
                                @endphp
                                <td class="border border-slate-300 py-2 px-3 text-slate-700">{{ $ansText }}</td>
                            @endforeach
                        @endif
                        <td class="border border-slate-300 py-2 px-3 text-center font-mono text-slate-700">{{ substr($att->attended_time, 0, 5) }}</td>
                        <td class="border border-slate-300 py-1 px-2 text-center">
                            @if($att->signature_base64)
                                <img src="data:image/png;base64,{{ $att->signature_base64 }}" alt="TTD" class="max-h-8 max-w-[90px] mx-auto object-contain">
                            @else
                                <span class="text-[10px] text-slate-400 italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + (isset($customFields) ? $customFields->count() : 0) + 1 }}" class="border border-slate-300 py-8 text-center text-slate-500 italic">
                            Belum ada peserta yang mengisi daftar kehadiran.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Lembar Pengesahan -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs mt-10 pt-4 break-inside-avoid">
            <div>
                <p class="text-slate-700 leading-tight">Mengetahui,</p>
                <p class="font-semibold text-slate-800">Kepala {{ $settings['school_name'] ?? 'SMK Negeri 1 Majalengka' }}</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $settings['headmaster_name'] ?? 'Drs. H. Nono Mardiono, M.Pd.' }}</p>
                <p class="text-[11px] text-slate-500 font-mono">NIP. {{ $settings['headmaster_nip'] ?? '19680312 199403 1 008' }}</p>
            </div>
            <div>
                <p class="text-slate-700 leading-tight">Majalengka, {{ $meeting->meeting_date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}</p>
                <p class="font-semibold text-slate-800">Ketua Panitia Kegiatan</p>
                <div class="h-20"></div>
                <p class="font-bold text-slate-900 underline">{{ $meeting->creator?->name ?? 'Panitia Penyelenggara' }}</p>
                <p class="text-[11px] text-slate-500 font-mono">NIP. {{ $meeting->creator?->nip ?? '....................................' }}</p>
            </div>
        </div>

        <!-- Footer Identitas Sistem -->
        <div class="mt-12 pt-4 border-t border-dashed border-slate-200 text-[10px] text-slate-400 flex items-center justify-between">
            <span>DIHADIR, Digital Attendance Management System SMKN 1 Majalengka</span>
            <span>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
        </div>
    </div>
</body>
</html>
