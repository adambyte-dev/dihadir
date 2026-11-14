<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Terapkan header keamanan pada setiap respons aplikasi.
     *
     * Catatan: Content-Security-Policy sengaja hanya membatasi frame, base URI,
     * object, dan target form. Pembatasan script penuh belum diterapkan karena
     * Alpine.js mengevaluasi ekspresi inline (membutuhkan 'unsafe-eval').
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hilangkan header identifikasi server PHP untuk mencegah reconnaissance penyerang
        if (function_exists('header_remove')) {
            @header_remove('X-Powered-By');
        }

        $response = $next($request);

        // Hapus header X-Powered-By dari respons Symfony jika terpasang
        $response->headers->remove('X-Powered-By');

        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=(), usb=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
            'Cross-Origin-Resource-Policy' => 'same-origin',
            'Content-Security-Policy' => "frame-ancestors 'self'; base-uri 'self'; object-src 'none'; form-action 'self'",
            // Halaman publik boleh diindeks; rute privat/administratif ditandai noindex.
            'X-Robots-Tag' => $this->resolveRobotsTag($request),
        ];

        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($headers as $key => $value) {
            if ($value !== null && ! $response->headers->has($key)) {
                $response->headers->set($key, $value);
            }
        }

        // Halaman terautentikasi tidak boleh tersimpan di cache browser/proxy
        // agar data peserta tidak terbaca setelah sesi berakhir.
        if ($request->hasSession() && $request->user()) {
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }

    /**
     * Tentukan direktif X-Robots-Tag berdasarkan sensitivitas halaman.
     * Rute publik (Landing, Kebijakan Privasi, Syarat Ketentuan) diizinkan diindeks.
     * Rute privat/administratif (Admin, Form Absensi Token, Login) ditandai noindex.
     */
    private function resolveRobotsTag(Request $request): ?string
    {
        $path = trim($request->path(), '/');

        // Halaman publik yang ditujukan untuk crawling Google
        if ($path === '' || $path === 'kebijakan-privasi' || $path === 'syarat-ketentuan' || $path === 'sitemap.xml' || $path === 'robots.txt' || $path === 'llms.txt') {
            return 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        }

        // Rute privat, formulir pertemuan ber-token, login, atau panel admin
        return 'noindex, nofollow, noarchive';
    }
}
