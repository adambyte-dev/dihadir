<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ParticipantLoginRequest;
use App\Models\Meeting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ParticipantLoginController extends Controller
{
    /**
     * Show the participant attendance login form.
     */
    public function showLoginForm(Request $request, ?string $token = null): View|RedirectResponse
    {
        $meetingToken = $token ?: $request->query('meeting');
        $meeting = null;

        if ($meetingToken) {
            $meeting = Meeting::where('public_token', $meetingToken)->first();

            if (! $meeting) {
                return redirect()->route('absensi.login')
                    ->with('error', 'Tautan pertemuan tidak dikenali atau sudah tidak berlaku. Silakan pindai ulang QR Code kegiatan.');
            }
        }

        // Sesi peserta yang akunnya sudah dinonaktifkan harus diakhiri.
        $currentParticipant = Auth::guard('participant')->user();
        if ($currentParticipant && ! $currentParticipant->is_active) {
            Auth::guard('participant')->logout();
            $currentParticipant = null;
        }

        // Jika peserta sudah login dan ada meeting, langsung arahkan ke form pertemuan
        if ($currentParticipant && $meeting) {
            return redirect()->route('absensi.form', $meeting->public_token);
        }

        // Siapkan token verifikasi geser
        $slideToken = Str::random(32);
        session(['slide_verify_token' => $slideToken]);

        return view('auth.participant-login', compact('meeting', 'slideToken'));
    }

    /**
     * Handle incoming participant authentication request.
     */
    public function login(ParticipantLoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var \App\Models\Participant|null $participant */
        $participant = Auth::guard('participant')->user();

        AuditLogger::logFor(null, 'participant_login_succeeded', $participant, [
            'participant_id' => $participant?->id,
            'nip' => $participant?->nip,
        ]);

        $meetingToken = (string) $request->input('meeting_token');

        if ($meetingToken !== '') {
            $meeting = Meeting::where('public_token', $meetingToken)->first();

            if ($meeting) {
                return redirect()->route('absensi.form', $meeting->public_token)
                    ->with('success', 'Berhasil masuk! Silakan lengkapi formulir absensi pertemuan.');
            }
        }

        return redirect()->route('absensi.login')
            ->with('success', 'Berhasil masuk. Silakan pindai QR Code atau buka tautan kegiatan yang Anda ikuti.');
    }

    /**
     * Log the participant out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('participant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('absensi.login')
            ->with('info', 'Sesi absensi Anda telah selesai.');
    }
}
