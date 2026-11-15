<?php

namespace App\Http\Middleware;

use App\Http\Controllers\SecurityChallengeController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceSecurityChallenge
{
    /**
     * Jalankan pemeriksaan verifikasi keamanan sebelum membuka halaman web.
     * Mesin pencari resmi (Googlebot, Bingbot) dilewatkan agar SEO sitemap & SERP tetap 100% aman.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 0. Lewatkan unit testing / artisan CLI otomatis
        if (app()->environment('testing')) {
            return $next($request);
        }

        // 1. Lewatkan rute verifikasi itu sendiri
        if ($request->is('cdn-cgi/challenge*') || $request->is('cdn-cgi/verify*')) {
            return $next($request);
        }

        // 2. Lewatkan aset statis publik, sitemap, robots, llms, favicon
        if (
            $request->is('build/*') ||
            $request->is('images/*') ||
            $request->is('avatar/*') ||
            $request->is('robots.txt') ||
            $request->is('sitemap.xml') ||
            $request->is('llms.txt') ||
            $request->is('site.webmanifest') ||
            $request->is('favicon*') ||
            $request->is('apple-touch-icon*')
        ) {
            return $next($request);
        }

        // 3. Lewatkan search engine bot resmi (Googlebot, Bingbot, dll) agar SEO tidak terganggu
        $userAgent = strtolower((string) $request->header('User-Agent', ''));
        $searchBots = ['googlebot', 'bingbot', 'yandexbot', 'duckduckbot', 'baiduspider', 'facebookexternalhit', 'slurp'];
        foreach ($searchBots as $bot) {
            if (str_contains($userAgent, $bot)) {
                return $next($request);
            }
        }

        // 4. Jika user sudah login (Admin atau Peserta) atau sudah memiliki cookie clearance valid, teruskan
        if (auth()->check() || auth()->guard('participant')->check() || SecurityChallengeController::isVerified($request)) {
            return $next($request);
        }

        // 5. Belum terverifikasi: arahkan ke halaman challenge
        $target = $request->fullUrl();
        return redirect()->route('security.challenge', ['r' => $target]);
    }
}
