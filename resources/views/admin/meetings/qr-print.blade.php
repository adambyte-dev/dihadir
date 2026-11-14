<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cetak QR Standee Absensi - {{ $meeting->title }}</title>
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
            .print-standee {
                border: 2px solid #0F172A !important;
                box-shadow: none !important;
                margin: 0 auto !important;
                page-break-inside: avoid !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 antialiased py-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-xl mx-auto mb-6 px-4 flex items-center justify-between">
        <a 
            href="{{ route('admin.pertemuan.show', $meeting) }}" 
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white px-3 py-2 rounded-lg border border-slate-300 shadow-2xs transition"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Kembali ke Detail Pertemuan</span>
        </a>

        <div class="flex items-center gap-2">
            <a 
                href="{{ route('admin.pertemuan.qr.download', $meeting) }}" 
                class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold rounded-lg bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 transition shadow-2xs"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Unduh SVG</span>
            </a>

            <button 
                type="button" 
                onclick="window.print()" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs transition cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                <span>Cetak Standee QR</span>
            </button>
        </div>
    </div>

    <!-- Lembar Standee Cetak A4 -->
    <div class="print-standee max-w-xl mx-auto bg-white rounded-2xl border-2 border-slate-900 shadow-xl overflow-hidden text-center">
        
        <!-- Header Banner Standee -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-7">
            <div class="flex items-center justify-center gap-3.5 mb-3">
                <div class="bg-white rounded-xl p-1.5 shadow-md">
                    <img src="{{ asset('images/logo-smkn1majalengka.png') }}" alt="SMKN 1 Majalengka" class="h-12 w-12 object-contain">
                </div>
                <div class="h-8 w-px bg-white/20"></div>
                <div class="bg-white rounded-xl px-2.5 py-1.5 shadow-md">
                    <img src="{{ asset('images/logo-dihadir-full-trimmed.png') }}" alt="DIHADIR" class="h-7 w-auto object-contain">
                </div>
            </div>

            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-blue-300">Presensi Kehadiran Digital</p>
            <h1 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-1">{{ $meeting->title }}</h1>
            <p class="text-xs text-slate-300 mt-1">{{ $meeting->organizer }}</p>
        </div>

        <!-- Badan QR Standee -->
        <div class="p-8 sm:p-10 space-y-6">
            <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3.5 py-1 border border-blue-200">
                <svg class="w-4 h-4 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-800">Pindai Menggunakan Kamera HP</span>
            </div>

            <!-- Frame Box QR Code Besar -->
            <div class="mx-auto w-72 sm:w-80 p-5 rounded-2xl bg-white border-2 border-dashed border-slate-300 shadow-sm flex items-center justify-center">
                <img
                    src="{{ route('admin.pertemuan.qr', ['meeting' => $meeting, 'size' => 450, 'v' => 2]) }}"
                    alt="QR Code {{ $meeting->title }}"
                    class="w-full h-full object-contain"
                >
            </div>

            <!-- Instruksi Peserta 3 Langkah -->
            <div class="grid grid-cols-3 gap-3 text-left pt-2">
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-center">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white mb-1.5">1</span>
                    <p class="text-xs font-bold text-slate-800">Scan QR</p>
                    <p class="text-[10.5px] text-slate-500 leading-tight mt-0.5">Buka kamera HP atau pemindai QR.</p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-center">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white mb-1.5">2</span>
                    <p class="text-xs font-bold text-slate-800">Isi Formulir</p>
                    <p class="text-[10.5px] text-slate-500 leading-tight mt-0.5">Lengkapi identitas & instansi asal.</p>
                </div>
                <div class="rounded-xl bg-slate-50 border border-slate-200 p-3 text-center">
                    <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-blue-600 text-[11px] font-bold text-white mb-1.5">3</span>
                    <p class="text-xs font-bold text-slate-800">Tanda Tangan</p>
                    <p class="text-[10.5px] text-slate-500 leading-tight mt-0.5">Bubuhkan tanda tangan langsung di HP.</p>
                </div>
            </div>

            <!-- Detail Pelaksanaan -->
            <div class="rounded-xl bg-slate-50 border border-slate-200/90 p-3.5 text-xs text-slate-700">
                <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-1 font-medium">
                    <span><strong>Hari:</strong> {{ $meeting->meeting_date?->translatedFormat('l, d F Y') }}</span>
                    <span>&bull;</span>
                    <span><strong>Waktu:</strong> {{ substr($meeting->start_time, 0, 5) }} - {{ substr($meeting->end_time, 0, 5) }} WIB</span>
                    <span>&bull;</span>
                    <span><strong>Tempat:</strong> {{ $meeting->location }}</span>
                </div>
            </div>

            <!-- Link Manual -->
            <div class="pt-1 text-[11px] text-slate-500">
                <p>Atau akses formulir melalui tautan:</p>
                <p class="font-mono font-bold text-slate-800 break-all select-all mt-0.5">{{ $meeting->public_url }}</p>
            </div>
        </div>

        <!-- Footer Standee -->
        <div class="bg-slate-900 text-white/80 py-3 px-6 text-[10.5px] flex items-center justify-between border-t border-slate-800">
            <span>SMK Negeri 1 Majalengka</span>
            <span>DIHADIR — Digital Attendance Management System</span>
        </div>
    </div>
</body>
</html>