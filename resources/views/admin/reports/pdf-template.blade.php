<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Berita Acara Kehadiran - {{ $meeting->title }}</title>
    <style>
        @page {
            margin: {{ $isLandscape ? '1.0cm 1.2cm 1.2cm 1.2cm' : '1.2cm 1.5cm 1.5cm 1.5cm' }};
            size: a4 {{ $isLandscape ? 'landscape' : 'portrait' }};
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: {{ $isLandscape ? '8.5pt' : '9pt' }};
            line-height: 1.35;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        /* KOP SURAT */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }
        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }
        .kop-logo {
            width: 65px;
            text-align: center;
        }
        .kop-logo img {
            width: 60px;
            height: auto;
        }
        .kop-text {
            text-align: center;
            padding-right: 20px;
        }
        .kop-gov {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .kop-dept {
            font-size: 10.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .kop-school {
            font-size: 13pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 2px 0;
        }
        .kop-address {
            font-size: 7.5pt;
            color: #475569;
            line-height: 1.3;
        }

        .line-thick {
            border-top: 2.5px solid #0f172a;
            margin-top: 6px;
            margin-bottom: 1.5px;
        }
        .line-thin {
            border-top: 1px solid #0f172a;
            margin-bottom: 12px;
        }

        /* JUDUL DOKUMEN */
        .doc-title-container {
            text-align: center;
            margin-bottom: 12px;
        }
        .doc-title {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #0f172a;
        }
        .doc-subtitle {
            font-size: 8pt;
            color: #64748b;
            margin-top: 2px;
        }

        /* METADATA KEGIATAN */
        .meta-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        .meta-box td {
            padding: 4px 8px;
            vertical-align: top;
        }
        .meta-label {
            width: 110px;
            font-weight: bold;
            color: #334155;
        }
        .meta-sep {
            width: 10px;
            text-align: center;
            color: #64748b;
        }
        .meta-val {
            color: #0f172a;
        }

        /* TABEL KEHADIRAN */
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: {{ $isLandscape ? '7.5pt' : '8pt' }};
        }
        .content-table th, .content-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 5px;
            vertical-align: middle;
            word-wrap: break-word;
        }
        .content-table th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-align: center;
            font-size: 7.5pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .content-table tbody tr:nth-child(even) {
            background-color: #fafbfc;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        .sig-cell {
            text-align: center;
            padding: 2px !important;
            width: 75px;
            height: 34px;
        }
        .sig-cell img {
            max-height: 32px;
            max-width: 70px;
            object-fit: contain;
        }

        /* LEMBAR PENGESAHAN */
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            page-break-inside: avoid;
            font-size: 8.5pt;
        }
        .signoff-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
            padding: 0 10px;
        }
        .signoff-space {
            height: 48px;
        }
        .signoff-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .signoff-nip {
            font-size: 8pt;
            color: #475569;
            margin-top: 2px;
        }

        /* FOOTER */
        .footer-note {
            margin-top: 20px;
            font-size: 7pt;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- KOP Surat Resmi SMKN 1 Majalengka -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                @if($schoolLogoBase64)
                    <img src="data:image/png;base64,{{ $schoolLogoBase64 }}" alt="Logo SMKN 1 Majalengka">
                @endif
            </td>
            <td class="kop-text">
                <div class="kop-gov">Pemerintah Daerah Provinsi Jawa Barat</div>
                <div class="kop-dept">Dinas Pendidikan &bull; Cabang Dinas Pendidikan Wilayah IX</div>
                <div class="kop-school">{{ $settings['school_name'] ?? 'SMK Negeri 1 Majalengka' }}</div>
                <div class="kop-address">
                    {{ $settings['school_address'] ?? 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401' }}
                    @if(!empty($settings['school_phone'])) Telp. {{ $settings['school_phone'] }} @endif
                    &bull; E-mail: {{ $settings['school_email'] ?? 'info@smkn1majalengka.sch.id' }}
                </div>
            </td>
        </tr>
    </table>
    <div class="line-thick"></div>
    <div class="line-thin"></div>

    <!-- Judul Dokumen -->
    <div class="doc-title-container">
        <div class="doc-title">Berita Acara &amp; Daftar Hadir Pertemuan</div>
        <div class="doc-subtitle">Sistem Presensi Digital (DIHADIR) &bull; SMKN 1 Majalengka</div>
    </div>

    <!-- Informasi Acara / Pertemuan -->
    <table class="meta-box">
        <tr>
            <td class="meta-label">Kegiatan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $meeting->title }}</strong></td>
            <td class="meta-label">Tanggal</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->meeting_date?->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Penyelenggara</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->organizer }}</td>
            <td class="meta-label">Waktu</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ substr($meeting->start_time, 0, 5) }} - {{ substr($meeting->end_time, 0, 5) }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Lokasi</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->location }}</td>
            <td class="meta-label">Total Hadir</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $attendances->count() }} Orang</strong></td>
        </tr>
    </table>

    <!-- Tabel Daftar Kehadiran -->
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th style="width: 85px;">NIP</th>
                <th>Nama Lengkap</th>
                <th style="width: 105px;">Unit Kerja</th>
                <th style="width: 75px;">Kab./Kota</th>
                @if(isset($customFields) && $customFields->isNotEmpty())
                    @php
                        // Filter out fields whose label already corresponds to standard columns to avoid duplication
                        $reportFields = $customFields->filter(function($f) {
                            $low = strtolower($f->label . ' ' . $f->field_key);
                            return !str_contains($low, 'tanda tangan') && !str_contains($low, 'signature');
                        });
                    @endphp
                    @foreach($reportFields as $cField)
                        <th>{{ $cField->label }}</th>
                    @endforeach
                @else
                    @php $reportFields = collect(); @endphp
                @endif
                <th style="width: 45px;">Waktu</th>
                <th style="width: 75px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $att)
                @php
                    $answersMap = $att->customAnswers->keyBy('meeting_form_field_id');
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center font-mono">{{ $att->nip ?: '-' }}</td>
                    <td><strong>{{ $att->name }}</strong></td>
                    <td>{{ $att->unit_kerja ?: '-' }}</td>
                    <td>{{ $att->kabupaten_kota ?: '-' }}</td>
                    @if($reportFields->isNotEmpty())
                        @foreach($reportFields as $cField)
                            @php
                                $ans = $answersMap->get($cField->id);
                                $ansText = $ans ? ($ans->answer_value ?? ($ans->file_path ? '[Lampiran]' : '-')) : '-';
                            @endphp
                            <td>{{ $ansText }}</td>
                        @endforeach
                    @endif
                    <td class="text-center font-mono">{{ substr((string) $att->attended_time, 0, 5) }}</td>
                    <td class="sig-cell">
                        @if($att->signature_base64)
                            <img src="data:image/png;base64,{{ $att->signature_base64 }}" alt="TTD">
                        @else
                            <span style="font-size: 7pt; color: #94a3b8;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 6 + ($reportFields->count()) + 1 }}" class="text-center" style="padding: 18px; color: #64748b;">
                        <em>Belum ada peserta yang mengisi daftar kehadiran.</em>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Lembar Pengesahan -->
    <table class="signoff-table">
        <tr>
            <td>
                Mengetahui,<br>
                Kepala {{ $settings['school_name'] ?? 'SMK Negeri 1 Majalengka' }}
                <div class="signoff-space"></div>
                <div class="signoff-name">{{ $settings['headmaster_name'] ?? 'Drs. H. Nono Mardiono, M.Pd.' }}</div>
                <div class="signoff-nip">NIP. {{ $settings['headmaster_nip'] ?? '19680312 199403 1 008' }}</div>
            </td>
            <td>
                Majalengka, {{ $meeting->meeting_date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}<br>
                Ketua Panitia Kegiatan
                <div class="signoff-space"></div>
                <div class="signoff-name">{{ $meeting->creator?->name ?? 'Panitia Penyelenggara' }}</div>
                <div class="signoff-nip">NIP. {{ $meeting->creator?->nip ?? '....................................' }}</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        Dokumen dicetak otomatis dari sistem <strong>DIHADIR</strong> pada {{ $printedAt }}. Keabsahan tanda tangan tercatat secara digital pada server SMKN 1 Majalengka.
    </div>
</body>
</html>
