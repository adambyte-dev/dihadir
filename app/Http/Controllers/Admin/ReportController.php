<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display reporting dashboard with meeting list and export options.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $meetings = Meeting::forUser($user)
            ->withCount(['attendances as valid_attendances_count' => function ($q) {
                $q->where('status', 'valid');
            }])
            ->orderBy('meeting_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reports.index', compact('meetings'));
    }

    /**
     * Export formal PDF attendance report with official school header.
     */
    public function exportPdf(Request $request, Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $attendances = $meeting->attendances()
            ->with('customAnswers.formField')
            ->where('status', 'valid')
            ->orderBy('attended_time', 'asc')
            ->get();

        // Siapkan tanda tangan dalam format base64 agar aman di-render oleh Dompdf
        foreach ($attendances as $att) {
            if ($att->signature_path && Storage::disk('local')->exists($att->signature_path)) {
                $att->signature_base64 = base64_encode(Storage::disk('local')->get($att->signature_path));
            } else {
                $att->signature_base64 = null;
            }
        }

        // Siapkan logo KOP dalam base64
        $schoolLogoPath = public_path('images/logo-smkn1majalengka.png');
        $schoolLogoBase64 = file_exists($schoolLogoPath) ? base64_encode(file_get_contents($schoolLogoPath)) : null;

        $dihadirLogoPath = public_path('images/logo-dihadir-icon.png');
        $dihadirLogoBase64 = file_exists($dihadirLogoPath) ? base64_encode(file_get_contents($dihadirLogoPath)) : null;

        $settings = [
            'school_name' => SystemSetting::get('school_name', 'SMK Negeri 1 Majalengka'),
            'school_address' => SystemSetting::get('school_address', 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401'),
            'school_phone' => SystemSetting::get('school_phone', '(0233) 281484'),
            'school_email' => SystemSetting::get('school_email', 'info@smkn1majalengka.sch.id'),
            'headmaster_name' => SystemSetting::get('headmaster_name', 'Drs. H. Nono Mardiono, M.Pd.'),
            'headmaster_nip' => SystemSetting::get('headmaster_nip', '19680312 199403 1 008'),
        ];

        AuditLogger::log('report_exported_pdf', $meeting, [
            'meeting_id' => $meeting->id,
            'meeting_title' => $meeting->title,
            'total_attendances' => $attendances->count(),
        ]);

        // Kolom laporan mengikuti seluruh field formulir agar jawaban historis dari
        // pertanyaan yang kemudian dinonaktifkan tetap ikut terlaporkan.
        $customFields = $meeting->formFields()->orderBy('order_index')->get();

        // Landscape bila total kolom banyak (>= 2 custom fields) agar tidak terpotong
        $isLandscape = $customFields->count() >= 2;

        $pdf = Pdf::loadView('admin.reports.pdf-template', [
            'meeting' => $meeting,
            'attendances' => $attendances,
            'customFields' => $customFields,
            'schoolLogoBase64' => $schoolLogoBase64,
            'dihadirLogoBase64' => $dihadirLogoBase64,
            'settings' => $settings,
            'isLandscape' => $isLandscape,
            'printedAt' => now()->translatedFormat('d F Y, H:i') . ' WIB',
        ])
            ->setPaper('a4', $isLandscape ? 'landscape' : 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->setOption('isHtml5ParserEnabled', true);

        if ($request->query('preview') == 1) {
            return $pdf->stream("Laporan_Kehadiran_{$meeting->slug}.pdf");
        }

        return $pdf->download("Laporan_Kehadiran_{$meeting->slug}.pdf");
    }

    /**
     * Export attendance data as sanitized CSV (Formula-Injection Safe).
     */
    public function exportCsv(Request $request, Meeting $meeting): StreamedResponse
    {
        $this->authorize('view', $meeting);

        $statusFilter = (string) $request->query('status', 'valid');
        if (! in_array($statusFilter, ['valid', 'flagged', 'cancelled', 'all'], true)) {
            $statusFilter = 'valid';
        }

        $query = $meeting->attendances()
            ->with('customAnswers.formField')
            ->orderBy('attended_time', 'asc');
        if ($statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }
        $attendances = $query->get();
        $customFields = $meeting->formFields()->orderBy('order_index')->get();

        $timeRange = substr((string) $meeting->start_time, 0, 5)
            . ($meeting->end_time ? ' - ' . substr((string) $meeting->end_time, 0, 5) : '')
            . ' WIB';

        AuditLogger::log('report_exported_csv', $meeting, [
            'meeting_id' => $meeting->id,
            'meeting_title' => $meeting->title,
            'status_filter' => $statusFilter,
            'total_rows' => $attendances->count(),
        ]);

        $filename = "Rekap_Kehadiran_{$meeting->slug}_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($attendances, $meeting, $statusFilter, $customFields, $timeRange) {
            $handle = fopen('php://output', 'w');

            // Tulis UTF-8 BOM agar aksen dan karakter khusus terbaca sempurna di Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            $schoolName = SystemSetting::get('school_name', 'SMK Negeri 1 Majalengka');

            // Metadata Kegiatan
            fputcsv($handle, ['REKAP KEHADIRAN DIGITAL - ' . strtoupper($schoolName)]);
            fputcsv($handle, ['Nama Kegiatan:', self::sanitizeCsvValue($meeting->title)]);
            fputcsv($handle, ['Penyelenggara:', self::sanitizeCsvValue($meeting->organizer)]);
            fputcsv($handle, ['Hari & Tanggal:', $meeting->meeting_date?->translatedFormat('l, d F Y') ?? '-']);
            fputcsv($handle, ['Waktu Pelaksanaan:', $timeRange]);
            fputcsv($handle, ['Tempat / Lokasi:', self::sanitizeCsvValue($meeting->location)]);
            fputcsv($handle, ['Total Kehadiran:', $attendances->count() . ' Peserta']);
            fputcsv($handle, ['Filter Status:', $statusFilter === 'all' ? 'Semua Status' : ucfirst($statusFilter)]);
            fputcsv($handle, ['Waktu Ekspor:', now()->translatedFormat('d F Y, H:i:s') . ' WIB']);
            fputcsv($handle, []); // Baris kosong pemisah

            // Header Tabel Utama
            $headersRow = [
                'No',
                'NIP',
                'Nama Lengkap',
                'Unit Kerja / Instansi',
                'Kabupaten / Kota',
                'Tanggal',
                'Waktu (WIB)',
                'Status',
            ];

            foreach ($customFields as $field) {
                // Jangan duplikasi label bila mirip NIP/Nama/Unit Kerja yang sudah ada di kolom inti
                $headersRow[] = self::sanitizeCsvValue($field->label);
            }

            fputcsv($handle, $headersRow);

            $no = 1;
            foreach ($attendances as $row) {
                $answersMap = $row->customAnswers->keyBy('meeting_form_field_id');

                $dataRow = [
                    $no++,
                    self::sanitizeCsvValue($row->nip),
                    self::sanitizeCsvValue($row->name),
                    self::sanitizeCsvValue($row->unit_kerja),
                    self::sanitizeCsvValue($row->kabupaten_kota ?: '-'),
                    $row->attended_date?->format('d/m/Y') ?? '-',
                    substr((string) $row->attended_time, 0, 5) . ' WIB',
                    strtoupper((string) $row->status),
                ];

                foreach ($customFields as $field) {
                    $ans = $answersMap->get($field->id);
                    $val = '';
                    if ($ans) {
                        if ($ans->file_path) {
                            $val = '[Lampiran Berkas: ' . ($ans->answer_value ?: 'File') . ']';
                        } else {
                            $val = (string) $ans->answer_value;
                        }
                    }
                    $dataRow[] = self::sanitizeCsvValue($val);
                }

                fputcsv($handle, $dataRow);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display print-friendly web view with browser print dialog.
     */
    public function printView(Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $attendances = $meeting->attendances()
            ->with('customAnswers.formField')
            ->where('status', 'valid')
            ->orderBy('attended_time', 'asc')
            ->get();

        foreach ($attendances as $att) {
            if ($att->signature_path && Storage::disk('local')->exists($att->signature_path)) {
                $att->signature_base64 = base64_encode(Storage::disk('local')->get($att->signature_path));
            } else {
                $att->signature_base64 = null;
            }
        }

        $settings = [
            'school_name' => SystemSetting::get('school_name', 'SMK Negeri 1 Majalengka'),
            'school_address' => SystemSetting::get('school_address', 'Jalan Raya Tonjong - Pinangraja No. 55 Majalengka 45401'),
            'school_phone' => SystemSetting::get('school_phone', '(0233) 281484'),
            'school_email' => SystemSetting::get('school_email', 'info@smkn1majalengka.sch.id'),
            'headmaster_name' => SystemSetting::get('headmaster_name', 'Drs. H. Nono Mardiono, M.Pd.'),
            'headmaster_nip' => SystemSetting::get('headmaster_nip', '19680312 199403 1 008'),
        ];

        $customFields = $meeting->formFields()->orderBy('order_index')->get();

        return view('admin.reports.print-preview', compact('meeting', 'attendances', 'customFields', 'settings'));
    }

    /**
     * Sanitize field value to prevent CSV Formula Injection attacks.
     * Prefixes potentially malicious formula characters (=, +, -, @, tab, cr) with a single quote.
     */
    public static function sanitizeCsvValue(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $trimmed = trim($value);
        $firstChar = substr($trimmed, 0, 1);

        if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
            return "'" . $trimmed;
        }

        return $trimmed;
    }
}
