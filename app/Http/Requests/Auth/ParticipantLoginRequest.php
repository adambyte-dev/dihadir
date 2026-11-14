<?php

namespace App\Http\Requests\Auth;

use App\Services\AuditLogger;
use App\Services\PuzzleCaptchaService;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ParticipantLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nip' => ['required', 'string'],
            'password' => ['required', 'string'],
            'slider_token' => ['required', 'string'],
            'meeting_token' => ['nullable', 'string'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'nip.required' => 'Nomor Induk Pegawai (NIP) wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'slider_token.required' => 'Silakan geser tombol verifikasi keamanan ke kanan sebelum masuk.',
        ];
    }

    /**
     * Authenticate participant credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // 1. Validasi Token Verifikasi Geser (Mandiri: Puzzle Captcha / Legacy Slide)
        $sliderToken = (string) $this->input('slider_token');
        $expectedLegacyToken = session('slide_verify_token');

        if (str_starts_with($sliderToken, PuzzleCaptchaService::PREFIX . '.')) {
            $puzzleService = app(PuzzleCaptchaService::class);
            $verification = $puzzleService->verify($sliderToken);

            if (! $verification['valid']) {
                throw ValidationException::withMessages([
                    'slider_token' => $verification['message'],
                ]);
            }
        } elseif (empty($sliderToken) || ($expectedLegacyToken && $sliderToken !== $expectedLegacyToken)) {
            throw ValidationException::withMessages([
                'slider_token' => 'Verifikasi geser tidak valid atau kedaluwarsa. Silakan geser kembali potongan puzzle.',
            ]);
        }

        // 2. Kredensial Otentikasi Peserta
        $credentials = [
            'nip' => $this->input('nip'),
            'password' => $this->input('password'),
        ];

        if (! Auth::guard('participant')->attempt($credentials)) {
            RateLimiter::hit($this->throttleKey());

            AuditLogger::logFor(null, 'participant_login_failed', null, [
                'nip' => Str::limit((string) $credentials['nip'], 32),
                'reason' => 'invalid_credentials',
            ]);

            throw ValidationException::withMessages([
                'nip' => 'NIP atau kata sandi yang Anda masukkan tidak cocok dengan data terdaftar.',
            ]);
        }

        $participant = Auth::guard('participant')->user();

        if (! $participant->is_active) {
            Auth::guard('participant')->logout();
            RateLimiter::hit($this->throttleKey());

            AuditLogger::logFor(null, 'participant_login_failed', null, [
                'nip' => Str::limit((string) $credentials['nip'], 32),
                'reason' => 'inactive_account',
            ]);

            throw ValidationException::withMessages([
                'nip' => 'Akun peserta Anda dinonaktifkan. Silakan hubungi Panitia atau IT Support SMKN 1 Majalengka.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        session()->forget('slide_verify_token');
    }

    /**
     * Rate limiting check.
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Throttle key.
     */
    public function throttleKey(): string
    {
        // Input mentah bisa berbentuk array; jangan sampai memicu error server.
        $nip = $this->input('nip');
        $nip = is_scalar($nip) ? (string) $nip : '';

        return Str::transliterate(Str::lower(Str::limit($nip, 32, '')).'|'.$this->ip());
    }
}
