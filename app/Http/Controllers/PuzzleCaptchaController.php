<?php

namespace App\Http\Controllers;

use App\Services\PuzzleCaptchaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PuzzleCaptchaController extends Controller
{
    public function __construct(
        protected PuzzleCaptchaService $puzzleService
    ) {}

    /**
     * Endpoint untuk meminta tantangan puzzle baru.
     * Mengembalikan SVG visual utuh dengan lubang, SVG potongan puzzle, Y relatif, dan token terenkripsi.
     */
    public function challenge(): JsonResponse
    {
        $challenge = $this->puzzleService->challenge();

        return response()->json($challenge)
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Endpoint pengecekan instan saat slider digeser/dilepas (tanpa mengonsumsi token).
     */
    public function check(Request $request): JsonResponse
    {
        $payload = (string) $request->input('captcha', '');

        if ($payload === '') {
            return response()->json(['cocok' => false, 'message' => 'Payload kosong.'], 400);
        }

        $cocok = $this->puzzleService->check($payload);

        return response()->json([
            'cocok' => $cocok,
            'message' => $cocok ? 'Posisi cocok.' : 'Posisi belum pas.',
        ]);
    }
}
