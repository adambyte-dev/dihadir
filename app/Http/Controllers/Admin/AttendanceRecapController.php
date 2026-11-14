<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCustomAnswer;
use App\Models\Meeting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttendanceRecapController extends Controller
{
    /**
     * Display attendance recap per meeting.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // 1. Ambil daftar pertemuan yang diizinkan untuk admin ini
        $meetings = Meeting::forUser($user)
            ->orderBy('meeting_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $selectedMeetingId = $request->query('meeting_id');
        $selectedMeeting = null;

        if ($selectedMeetingId) {
            $selectedMeeting = $meetings->firstWhere('id', (int) $selectedMeetingId);
        }

        // Default: ambil pertemuan paling mutakhir jika belum ada yang dipilih
        if (! $selectedMeeting && $meetings->isNotEmpty()) {
            $selectedMeeting = $meetings->first();
        }

        $attendances = collect();
        $unattendedParticipants = collect();
        $stats = [
            'total_attended' => 0,
            'total_invited' => 0,
            'total_absent' => 0,
            'attendance_rate' => 0,
        ];

        $tab = $request->query('tab', 'hadir');
        $search = trim($request->query('q', ''));

        if ($selectedMeeting) {
            $this->authorize('view', $selectedMeeting);

            // Query Peserta Sudah Hadir
            $attendancesQuery = $selectedMeeting->attendances()
                ->with('customAnswers')
                ->orderBy('attended_time', 'desc')
                ->orderBy('id', 'desc');

            if ($search !== '') {
                $attendancesQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('unit_kerja', 'like', "%{$search}%")
                        ->orWhere('kabupaten_kota', 'like', "%{$search}%");
                });
            }

            $attendances = $attendancesQuery->paginate(20)->withQueryString();

            // Query Peserta Terdaftar Belum Hadir
            $unattendedQuery = $selectedMeeting->participants()
                ->whereDoesntHave('attendances', function ($q) use ($selectedMeeting) {
                    $q->where('meeting_id', $selectedMeeting->id);
                });

            if ($search !== '') {
                $unattendedQuery->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('unit_kerja', 'like', "%{$search}%");
                });
            }

            $unattendedParticipants = $unattendedQuery->paginate(20, ['*'], 'unattended_page')->withQueryString();

            // Hitung Statistik Riil
            $totalAttended = $selectedMeeting->attendances()->where('status', 'valid')->count();
            $totalInvited = $selectedMeeting->participants()->count();
            $totalAbsent = max(0, $totalInvited - $totalAttended);
            // Kehadiran anonim (tanpa undangan) tetap dihitung, tetapi persentase
            // dibatasi 100% agar ringkasan tidak menampilkan angka mustahil.
            $attendanceRate = $totalInvited > 0
                ? min(100, round(($totalAttended / $totalInvited) * 100, 1))
                : ($totalAttended > 0 ? 100 : 0);

            $stats = [
                'total_attended' => $totalAttended,
                'total_invited' => $totalInvited,
                'total_absent' => $totalAbsent,
                'attendance_rate' => $attendanceRate,
            ];
        }

        return view('admin.attendance.index', compact(
            'meetings',
            'selectedMeeting',
            'attendances',
            'unattendedParticipants',
            'stats',
            'tab',
            'search'
        ));
    }

    /**
     * Serve private signature image securely.
     */
    public function showSignature(Attendance $attendance): BinaryFileResponse
    {
        $meeting = $attendance->meeting;

        if (! $meeting) {
            abort(404, 'Pertemuan untuk data kehadiran ini tidak ditemukan.');
        }

        $this->authorize('view', $meeting);

        if (! $attendance->signature_path || ! Storage::disk('local')->exists($attendance->signature_path)) {
            abort(404, 'Berkas tanda tangan digital tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($attendance->signature_path);

        return response()->file($fullPath, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Serve a private custom-field attachment to authorized admins only.
     * Non-image files are always forced as downloads and sandboxed to prevent
     * active content (HTML/SVG/PDF script) from executing in the app origin.
     */
    public function showAttachment(AttendanceCustomAnswer $answer): BinaryFileResponse
    {
        $attendance = $answer->attendance;
        $meeting = $attendance?->meeting;

        if (! $attendance || ! $meeting) {
            abort(404, 'Data lampiran tidak ditemukan.');
        }

        $this->authorize('view', $meeting);

        if (! $answer->file_path || ! Storage::disk('local')->exists($answer->file_path)) {
            abort(404, 'Berkas lampiran tidak ditemukan.');
        }

        $fullPath = Storage::disk('local')->path($answer->file_path);
        $mime = Storage::disk('local')->mimeType($answer->file_path) ?: 'application/octet-stream';
        $isSafeImage = in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true);

        $originalName = (string) ($answer->answer_value ?: basename($answer->file_path));
        $safeName = trim((string) preg_replace('/[^A-Za-z0-9._ -]/', '_', basename($originalName))) ?: 'lampiran';

        return response()->file($fullPath, [
            'Content-Type' => $isSafeImage ? $mime : 'application/octet-stream',
            'Content-Disposition' => ($isSafeImage ? 'inline' : 'attachment') . '; filename="' . $safeName . '"',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'Cache-Control' => 'private, no-cache, no-store, must-revalidate',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    /**
     * Update attendance status (valid, flagged, cancelled).
     */
    public function updateStatus(Request $request, Attendance $attendance): RedirectResponse
    {
        $meeting = $attendance->meeting;

        if (! $meeting) {
            abort(404, 'Pertemuan untuk data kehadiran ini tidak ditemukan.');
        }

        $this->authorize('update', $meeting);

        $request->validate([
            'status' => 'required|in:valid,flagged,cancelled',
            'reason' => 'nullable|string|max:255',
        ]);

        $oldStatus = $attendance->status;
        $newStatus = $request->status;

        $attendance->update([
            'status' => $newStatus,
        ]);

        AuditLogger::log('attendance_status_updated', $attendance, [
            'meeting_id' => $attendance->meeting_id,
            'nip' => $attendance->nip,
            'name' => $attendance->name,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $request->input('reason'),
        ]);

        $label = match ($newStatus) {
            'valid' => 'divalidasi',
            'flagged' => 'ditandai (flagged)',
            'cancelled' => 'dibatalkan',
            default => $newStatus,
        };

        return back()->with('success', "Status kehadiran {$attendance->name} berhasil {$label}.");
    }
}
