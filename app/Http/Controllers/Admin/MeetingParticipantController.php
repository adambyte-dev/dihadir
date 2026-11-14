<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Participant;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MeetingParticipantController extends Controller
{
    /**
     * Display a listing of participants for the meeting (invited and available to invite).
     */
    public function index(Request $request, Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $search = trim($request->input('q', ''));
        $unitFilter = trim($request->input('unit_kerja', ''));

        // Daftar peserta yang sudah diundang pada pertemuan ini
        $invitedQuery = $meeting->participants()
            ->withPivot('status', 'created_at');

        if ($search !== '') {
            $invitedQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        if ($unitFilter !== '') {
            $invitedQuery->where('unit_kerja', $unitFilter);
        }

        $invitedParticipants = $invitedQuery->paginate(20, ['*'], 'invited_page')->withQueryString();

        // Peserta aktif yang BELUM diundang ke pertemuan ini
        $invitedIds = $meeting->participants()->pluck('participants.id');
        $availableQuery = Participant::where('is_active', true)
            ->whereNotIn('id', $invitedIds);

        if ($search !== '') {
            $availableQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('unit_kerja', 'like', "%{$search}%");
            });
        }

        $availableParticipants = $availableQuery->orderBy('name')->paginate(15, ['*'], 'avail_page')->withQueryString();

        // Daftar unit kerja unik untuk dropdown filter
        $units = Participant::whereNotNull('unit_kerja')
            ->where('unit_kerja', '!=', '')
            ->distinct()
            ->pluck('unit_kerja')
            ->sort()
            ->values();

        $stats = [
            'total_invited' => $meeting->participants()->count(),
            'total_attended' => $meeting->attendances()->where('status', 'valid')->count(),
            'total_available' => Participant::where('is_active', true)->whereNotIn('id', $invitedIds)->count(),
        ];

        return view('admin.meetings.participants', compact(
            'meeting',
            'invitedParticipants',
            'availableParticipants',
            'units',
            'stats',
            'search',
            'unitFilter'
        ));
    }

    /**
     * Store (invite) selected participants to the meeting.
     */
    public function store(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $request->validate([
            'participant_ids' => 'required|array|min:1',
            'participant_ids.*' => ['integer', Rule::exists('participants', 'id')->whereNull('deleted_at')],
        ]);

        $participantIds = $request->input('participant_ids');
        $existingCount = $meeting->participants()->whereIn('participants.id', $participantIds)->count();

        $dataToSync = [];
        foreach ($participantIds as $id) {
            $dataToSync[$id] = [
                'status' => 'invited',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        $meeting->participants()->syncWithoutDetaching($dataToSync);

        $addedCount = count($participantIds) - $existingCount;

        AuditLogger::log('meeting_participants_invited', $meeting, [
            'meeting_id' => $meeting->id,
            'title' => $meeting->title,
            'invited_count' => $addedCount,
        ]);

        return back()->with('success', "{$addedCount} peserta berhasil diundang ke pertemuan \"{$meeting->title}\".");
    }

    /**
     * Invite all active participants to this meeting at once.
     */
    public function syncAll(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $allActive = Participant::where('is_active', true)->pluck('id');
        $alreadyInvited = $meeting->participants()->pluck('participants.id');
        $newIds = $allActive->diff($alreadyInvited);

        if ($newIds->isEmpty()) {
            return back()->with('info', 'Semua peserta aktif sudah terdaftar dalam undangan pertemuan ini.');
        }

        $dataToSync = [];
        $now = now();
        foreach ($newIds as $id) {
            $dataToSync[$id] = [
                'status' => 'invited',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $meeting->participants()->syncWithoutDetaching($dataToSync);

        AuditLogger::log('meeting_all_participants_invited', $meeting, [
            'meeting_id' => $meeting->id,
            'title' => $meeting->title,
            'invited_count' => $newIds->count(),
        ]);

        return back()->with('success', "Berhasil mengundang {$newIds->count()} peserta aktif ke pertemuan ini.");
    }

    /**
     * Remove an invited participant from the meeting.
     */
    public function destroy(Request $request, Meeting $meeting, Participant $participant): RedirectResponse
    {
        $this->authorize('update', $meeting);

        // Pastikan peserta memang terdaftar pada undangan pertemuan ini.
        if (! $meeting->participants()->whereKey($participant->id)->exists()) {
            return back()->with('error', 'Peserta tersebut tidak terdaftar dalam undangan pertemuan ini.');
        }

        // Jangan izinkan hapus jika peserta sudah mengisi absensi
        $hasAttendance = $meeting->attendances()->where('nip', $participant->nip)->exists();
        if ($hasAttendance) {
            return back()->with('error', "Peserta {$participant->name} tidak dapat dihapus karena telah memiliki catatan kehadiran pada pertemuan ini.");
        }

        $meeting->participants()->detach($participant->id);

        AuditLogger::log('meeting_participant_removed', $meeting, [
            'meeting_id' => $meeting->id,
            'participant_id' => $participant->id,
            'participant_name' => $participant->name,
        ]);

        return back()->with('success', "Undangan untuk peserta {$participant->name} berhasil dihapus.");
    }
}
