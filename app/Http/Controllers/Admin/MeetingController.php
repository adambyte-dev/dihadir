<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMeetingRequest;
use App\Http\Requests\Admin\UpdateMeetingRequest;
use App\Models\Meeting;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MeetingController extends Controller
{
    /**
     * Display a listing of meetings.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Meeting::forUser($user)
            ->with(['creator', 'admins'])
            ->latest('meeting_date');

        // Filter status kegiatan
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter status formulir
        if ($request->filled('form_status')) {
            $query->where('form_status', $request->input('form_status'));
        }

        // Pencarian nama kegiatan, lokasi, penyelenggara
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('organizer', 'like', "%{$search}%");
            });
        }

        $meetings = $query->paginate(10)->withQueryString();

        return view('admin.meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create(Request $request): View
    {
        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.meetings.create', compact('admins'));
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(StoreMeetingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Kegiatan draf/arsip tidak boleh langsung menerbitkan formulir publik.
        if ($validated['form_status'] === 'published' && in_array($validated['status'], ['draft', 'archived'], true)) {
            return back()->withInput()->withErrors([
                'form_status' => 'Formulir tidak dapat diterbitkan selama status kegiatan masih draf atau arsip.',
            ]);
        }

        $validated['created_by'] = $request->user()->id;

        $meeting = Meeting::create($validated);

        if ($request->filled('admin_ids')) {
            $meeting->admins()->sync((array) $request->input('admin_ids'));
        }

        AuditLogger::log('meeting_created', $meeting, [
            'meeting_id' => $meeting->id,
            'title' => $meeting->title,
        ]);

        return redirect()->route('admin.pertemuan.show', $meeting)
            ->with('success', 'Pertemuan "'.$meeting->title.'" berhasil dibuat.');
    }

    /**
     * Display the specified meeting details, links & QR.
     */
    public function show(Request $request, Meeting $meeting): View
    {
        $this->authorize('view', $meeting);

        $meeting->load(['creator', 'admins']);

        return view('admin.meetings.show', compact('meeting'));
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit(Request $request, Meeting $meeting): View
    {
        $this->authorize('update', $meeting);

        $meeting->load('admins');

        $admins = User::where('role', 'admin')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.meetings.edit', compact('meeting', 'admins'));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(UpdateMeetingRequest $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $validated = $request->validated();

        // Jaga konsistensi status: formulir terbit hanya untuk kegiatan yang sudah berjalan/terjadwal.
        if ($validated['form_status'] === 'published' && in_array($validated['status'], ['draft', 'archived'], true)) {
            return back()->withInput()->withErrors([
                'form_status' => 'Formulir tidak dapat diterbitkan selama status kegiatan masih draf atau arsip.',
            ]);
        }

        $meeting->update($validated);

        // Formulir edit selalu mengirim seluruh daftar admin terpilih, sehingga
        // mengosongkan semua centang harus benar-benar menghapus penugasan lama.
        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $meeting->admins()->sync((array) $request->input('admin_ids', []));
        }

        AuditLogger::log('meeting_updated', $meeting, [
            'meeting_id' => $meeting->id,
            'title' => $meeting->title,
        ]);

        return redirect()->route('admin.pertemuan.show', $meeting)
            ->with('success', 'Informasi pertemuan berhasil diperbarui.');
    }

    /**
     * Remove the specified meeting from storage (soft delete).
     */
    public function destroy(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('delete', $meeting);

        $title = $meeting->title;
        $meetingId = $meeting->id;
        $meeting->delete();

        AuditLogger::log('meeting_deleted', $meeting, [
            'meeting_id' => $meetingId,
            'title' => $title,
        ]);

        return redirect()->route('admin.pertemuan.index')
            ->with('success', 'Pertemuan "'.$title.'" berhasil dihapus.');
    }

    /**
     * Duplicate a meeting for recurring events.
     */
    public function duplicate(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('view', $meeting);

        $newMeeting = $meeting->replicate([
            'slug',
            'public_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);

        $newMeeting->title = $meeting->title . ' (Salinan)';
        $newMeeting->slug = Str::slug($newMeeting->title) . '-' . Str::lower(Str::random(6));
        $newMeeting->public_token = Str::random(32);
        $newMeeting->status = 'scheduled';
        $newMeeting->form_status = 'draft';
        $newMeeting->created_by = $request->user()->id;
        $newMeeting->save();

        // Copy assigned admins
        if ($meeting->admins()->exists()) {
            $newMeeting->admins()->sync($meeting->admins->pluck('id'));
        }

        // Copy custom form builder fields
        foreach ($meeting->formFields()->get() as $field) {
            $newMeeting->formFields()->create([
                'field_key' => $field->field_key,
                'label' => $field->label,
                'type' => $field->type,
                'description' => $field->description,
                'placeholder' => $field->placeholder,
                'is_required' => $field->is_required,
                'options' => $field->options,
                'order_index' => $field->order_index,
                'is_active' => $field->is_active,
            ]);
        }

        AuditLogger::log('meeting_duplicated', $newMeeting, [
            'original_id' => $meeting->id,
            'new_meeting_id' => $newMeeting->id,
            'title' => $newMeeting->title,
        ]);

        return redirect()->route('admin.pertemuan.edit', $newMeeting)
            ->with('success', 'Pertemuan berhasil digandakan. Silakan sesuaikan tanggal dan rincian kegiatan.');
    }

    /**
     * Toggle form status (publish / close).
     */
    public function toggleFormStatus(Request $request, Meeting $meeting): RedirectResponse
    {
        $this->authorize('update', $meeting);

        $newStatus = $meeting->form_status === 'published' ? 'closed' : 'published';

        if ($newStatus === 'published' && in_array($meeting->status, ['draft', 'archived'], true)) {
            return back()->with('error', 'Formulir tidak dapat diterbitkan selama status kegiatan masih draf atau diarsipkan. Ubah status kegiatan terlebih dahulu.');
        }

        $meeting->update(['form_status' => $newStatus]);

        AuditLogger::log('form_status_toggled', $meeting, [
            'meeting_id' => $meeting->id,
            'title' => $meeting->title,
            'form_status' => $newStatus,
        ]);

        $statusText = $newStatus === 'published' ? 'diterbitkan dan dibuka untuk pengisian' : 'ditutup';

        return back()->with('success', 'Status formulir berhasil diubah menjadi: '.$statusText.'.');
    }
}
