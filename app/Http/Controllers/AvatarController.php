<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AvatarService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AvatarController extends Controller
{
    /**
     * Tampilkan SVG avatar untuk pengguna terdaftar
     */
    public function showUser(User $user): Response
    {
        $svg = $user->avatar_svg;

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=604800, immutable',
        ]);
    }

    /**
     * Preview SVG avatar secara dinamis berdasarkan style & seed
     */
    public function preview(Request $request): Response
    {
        // Batasi panjang masukan agar cache avatar tidak dapat dibanjiri
        // variasi tak terbatas dari luar.
        $style = mb_substr((string) $request->query('style', 'lorelei'), 0, 32);
        $seed = mb_substr((string) $request->query('seed', 'preview'), 0, 60);

        $svg = AvatarService::renderSvg($style, $seed);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
