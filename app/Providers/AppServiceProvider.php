<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Seluruh URL produksi wajib HTTPS (tautan publik & QR Code).
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Role Gates
        Gate::define('manage-system', function (User $user) {
            return $user->isSuperAdmin() && $user->is_active;
        });

        Gate::define('manage-events', function (User $user) {
            return $user->isAdmin() && $user->is_active;
        });

        /*
         * Rate limit berlapis: dibatasi per alamat IP DAN per identitas.
         * Dengan begitu penyerang yang merotasi IP tetap tertahan oleh batas
         * per akun, sementara peserta sah di jaringan sekolah yang sama
         * (satu IP publik/NAT) tidak saling memblokir.
         */
        RateLimiter::for('admin-login', function (Request $request) {
            return [
                Limit::perMinute(6)->by('admin-login:ip:' . $request->ip()),
                Limit::perMinute(5)->by($this->rateLimitIdentity($request, 'login', 'admin-login:id:')),
            ];
        });

        RateLimiter::for('participant-login', function (Request $request) {
            return [
                Limit::perMinute(12)->by('participant-login:ip:' . $request->ip()),
                Limit::perMinute(5)->by($this->rateLimitIdentity($request, 'nip', 'participant-login:nip:')),
            ];
        });

        RateLimiter::for('attendance-submit', function (Request $request) {
            return [
                // Cukup longgar untuk antrean orang tua pada satu jaringan sekolah.
                Limit::perMinute(60)->by('attendance:ip:' . $request->ip()),
                // Batas ketat per NIP agar tidak ada pengiriman massal dari satu identitas.
                Limit::perMinute(6)->by($this->rateLimitIdentity($request, 'nip', 'attendance:nip:')),
            ];
        });

        RateLimiter::for('public-form', function (Request $request) {
            return Limit::perMinute(90)->by('public-form:ip:' . $request->ip());
        });
    }

    /**
     * Bangun kunci rate limit dari input identitas.
     *
     * Input selalu diperlakukan sebagai nilai mentah yang belum tervalidasi:
     * array atau string sangat panjang tidak boleh menghasilkan error server
     * maupun kunci cache yang melebihi batas penyimpanan.
     */
    private function rateLimitIdentity(Request $request, string $field, string $prefix): string
    {
        $value = $request->input($field);
        $value = is_scalar($value) ? (string) $value : '';

        return $prefix . Str::limit(Str::lower($value), 80, '');
    }
}
