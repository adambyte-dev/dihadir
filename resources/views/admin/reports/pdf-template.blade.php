<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kehadiran - {{ $meeting->title }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
            size: a4 portrait;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
        }
        .header-table td {
            vertical-align: middle;
            padding: 0;
        }
        .logo-col {
            width: 75px;
            text-align: center;
        }
        .text-col {
            text-align: center;
            padding-right: 40px;
        }
        .inst-prov {
            font-size: 10pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .inst-dinas {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #0f172a;
        }
        .inst-school {
            font-size: 14pt;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
            margin: 2px 0;
        }
        .inst-address {
            font-size: 8pt;
            color: #475569;
        }
        .divider-thick {
            border-top: 2.5px solid #000;
            margin-top: 8px;
            margin-bottom: 1.5px;
        }
        .divider-thin {
            border-top: 1px solid #000;
            margin-bottom: 16px;
        }
        .doc-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
            color: #0f172a;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 9pt;
        }
        .meta-table td {
            padding: 2.5px 4px;
            vertical-align: top;
        }
        .meta-label {
            width: 120px;
            font-weight: bold;
            color: #334155;
        }
        .meta-sep {
            width: 10px;
            text-align: center;
        }
        .meta-val {
            color: #0f172a;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
            font-size: 8.5pt;
        }
        .content-table th, .content-table td {
            border: 1px solid #cbd5e1;
            padding: 5px 6px;
            vertical-align: middle;
        }
        .content-table th {
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: bold;
            text-align: center;
            font-size: 8.5pt;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .sig-cell {
            text-align: center;
            width: 100px;
            height: 42px;
            padding: 2px !important;
        }
        .sig-cell img {
            max-height: 38px;
            max-width: 95px;
        }
        .signoff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            page-break-inside: avoid;
            font-size: 9pt;
        }
        .signoff-table td {
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        .signoff-space {
            height: 60px;
        }
        .signoff-name {
            font-weight: bold;
            text-decoration: underline;
            color: #0f172a;
        }
        .signoff-nip {
            font-size: 8pt;
            color: #475569;
        }
        .footer-note {
            margin-top: 30px;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <!-- KOP Surat Resmi SMKN 1 Majalengka -->
    <table class="header-table">
        <tr>
            <td class="logo-col">
                @if($schoolLogoBase64)
                    <img src="data:image/png;base64,{{ $schoolLogoBase64 }}" style="width: 68px; height: auto;" alt="Logo SMKN 1 Majalengka">
                @endif
            </td>
            <td class="text-col">
                <div class="inst-prov">Pemerintah Daerah Provinsi Jawa Barat</div>
                <div class="inst-dinas">Dinas Pendidikan, Cabang Dinas Pendidikan Wilayah IX</div>
                <div class="inst-school">{{ $settings['school_name'] ?? 'SMK Negeri 1 Majalengka' }}</div>
                <div class="inst-address">
                    {{ $settings['school_address'] ?? 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401' }}
                    @if(!empty($settings['school_phone'])) Telp. {{ $settings['school_phone'] }} @endif<br>
                    Website: www.smkn1majalengka.sch.id &bull; E-mail: {{ $settings['school_email'] ?? 'info@smkn1majalengka.sch.id' }}
                </div>
            </td>
        </tr>
    </table>
    <div class="divider-thick"></div>
    <div class="divider-thin"></div>

    <!-- Judul Dokumen -->
    <div class="doc-title">
        Daftar Hadir Kegiatan Pertemuan
    </div>

    <!-- Informasi Acara / Pertemuan -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nama Kegiatan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $meeting->title }}</strong></td>
        </tr>
        <tr>
            <td class="meta-label">Penyelenggara</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->organizer }}</td>
        </tr>
        <tr>
            <td class="meta-label">Hari / Tanggal</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->meeting_date?->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Waktu Pelaksanaan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ substr($meeting->start_time, 0, 5) }} - {{ substr($meeting->end_time, 0, 5) }} WIB</td>
        </tr>
        <tr>
            <td class="meta-label">Tempat / Ruangan</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">{{ $meeting->location }}</td>
        </tr>
    </table>

    <!-- Tabel Daftar Kehadiran -->
    <table class="content-table">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 100px;">NIP</th>
                <th>Nama Lengkap</th>
                <th style="width: 120px;">Unit Kerja</th>
                <th style="width: 80px;">Kab./Kota</th>
                @if(isset($customFields) && $customFields->isNotEmpty())
                    @foreach($customFields as $cField)
                        <th>{{ $cField->label }}</th>
                    @endforeach
                @endif
                <th style="width: 55px;">Waktu</th>
                <th style="width: 90px;">Tanda Tangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($attendances as $idx => $att)
                @php
                    $answersMap = $att->customAnswers->keyBy('meeting_form_field_id');
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $att->nip }}</td>
                    <td><strong>{{ $att->name }}</strong></td>
                    <td>{{ $att->unit_kerja }}</td>
                    <td>{{ $att->kabupaten_kota ?? '-' }}</td>
                    @if(isset($customFields) && $customFields->isNotEmpty())
                        @foreach($customFields as $cField)
                            @php
                                $ans = $answersMap->get($cField->id);
                                $ansText = $ans ? ($ans->answer_value ?? ($ans->file_path ? '[Lampiran]' : '-')) : '-';
                            @endphp
                            <td>{{ $ansText }}</td>
                        @endforeach
                    @endif
                    <td class="text-center" style="font-family: monospace;">{{ substr($att->attended_time, 0, 5) }}</td>
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
                    <td colspan="{{ 6 + (isset($customFields) ? $customFields->count() : 0) + 1 }}" class="text-center" style="padding: 20px; color: #64748b;">
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
        Dokumen ini diterbitkan secara otomatis oleh sistem <strong>DIHADIR</strong> (Digital Attendance Management System SMKN 1 Majalengka) pada {{ $printedAt }}. Keabsahan tanda tangan tercatat secara digital pada server sekolah.
    </div>
</body>
</html>
