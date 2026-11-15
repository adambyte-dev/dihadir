<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SecurityChallengeController extends Controller
{
    /**
     * Tampilkan halaman interstitial verifikasi keamanan (Cloudflare-style).
     */
    public function show(Request $request): Response
    {
        $target = $request->query('r', route('landing'));
        // Cegah open redirect: hanya izinkan path internal atau domain sendiri
        if (!str_starts_with($target, '/') && !str_starts_with($target, config('app.url'))) {
            $target = route('landing');
        }

        $rayId = 'cf-' . substr(md5($request->ip() . microtime(true)), 0, 16);

        return response()->view('security.challenge', [
            'target' => $target,
            'rayId' => $rayId,
            'clientIp' => $request->ip(),
        ])->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Verifikasi token tantangan keamanan dan terbitkan cookie kelolosan.
     */
    public function verify(Request $request): JsonResponse
    {
        $token = $request->input('token');
        $expected = hash_hmac('sha256', $request->ip() . date('Y-m-d H'), config('app.key'));

        if (!hash_equals($expected, (string) $token)) {
            return response()->json(['success' => false, 'message' => 'Token tidak valid'], 400);
        }

        // Cookie berlaku 24 jam (86400 detik)
        $cookieValue = hash_hmac('sha256', $request->ip() . '|verified', config('app.key'));
        cookie()->queue('cf_clearance_custom', $cookieValue, 1440, '/', null, true, true, false, 'Lax');

        return response()->json([
            'success' => true,
            'redirect' => $request->input('r', route('landing')),
        ]);
    }

    /**
     * Helper pembuatan token untuk client-side verifikasi.
     */
    public static function generateToken(string $ip): string
    {
        return hash_hmac('sha256', $ip . date('Y-m-d H'), config('app.key'));
    }

    /**
     * Helper validasi cookie clearance.
     */
    public static function isVerified(Request $request): bool
    {
        $cookie = $request->cookie('cf_clearance_custom');
        if (!$cookie) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->ip() . '|verified', config('app.key'));
        return hash_equals($expected, $cookie);
    }
}
