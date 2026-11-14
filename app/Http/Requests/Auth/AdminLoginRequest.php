<?php

namespace App\Http\Requests\Auth;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AdminLoginRequest extends FormRequest
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
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'slider_token' => ['nullable', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation messages in Indonesian.
     */
    public function messages(): array
    {
        return [
            'login.required' => 'Email atau NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'slider_token.required' => 'Silakan geser potongan puzzle verifikasi keamanan sebelum masuk.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // Verifikasi Puzzle Captcha (Kecuali pada pengujian otomatis yang tidak mengirim slider_token)
        $sliderToken = (string) $this->input('slider_token');
        if (! app()->environment('testing') || ! empty($sliderToken)) {
            if (empty($sliderToken)) {
                throw ValidationException::withMessages([
                    'slider_token' => 'Silakan geser potongan puzzle verifikasi keamanan hingga pas sebelum masuk.',
                ]);
            }

            if (str_starts_with($sliderToken, \App\Services\PuzzleCaptchaService::PREFIX . '.')) {
                $puzzleService = app(\App\Services\PuzzleCaptchaService::class);
                $verification = $puzzleService->verify($sliderToken);

                if (! $verification['valid']) {
                    throw ValidationException::withMessages([
                        'slider_token' => $verification['message'],
                    ]);
                }
            } else {
                throw ValidationException::withMessages([
                    'slider_token' => 'Verifikasi puzzle tidak sah atau telah kedaluwarsa. Silakan ulangi puzzle.',
                ]);
            }
        }

        $login = $this->input('login');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Allow login using email or NIP
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nip';
        $credentials = [
            $fieldType => $login,
            'password' => $password,
        ];

        if (! Auth::attempt($credentials, $remember)) {
            RateLimiter::hit($this->throttleKey());

            AuditLogger::logFor(null, 'admin_login_failed', null, [
                'login' => Str::limit((string) $login, 100),
                'reason' => 'invalid_credentials',
            ]);

            throw ValidationException::withMessages([
                'login' => 'Email/NIP atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $user = Auth::user();

        // Check active status
        if (! $user->is_active) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            AuditLogger::logFor(null, 'admin_login_failed', null, [
                'login' => Str::limit((string) $login, 100),
                'reason' => 'inactive_account',
            ]);

            throw ValidationException::withMessages([
                'login' => 'Akun Anda dinonaktifkan. Silakan hubungi Super Admin.',
            ]);
        }

        // Check if user is an admin or super_admin
        if (! $user->isAdmin()) {
            Auth::logout();
            RateLimiter::hit($this->throttleKey());

            AuditLogger::logFor(null, 'admin_login_failed', null, [
                'login' => Str::limit((string) $login, 100),
                'reason' => 'insufficient_role',
            ]);

            throw ValidationException::withMessages([
                'login' => 'Hanya akun pengelola (Admin / Super Admin) yang diizinkan masuk ke panel ini.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
