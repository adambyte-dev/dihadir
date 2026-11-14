<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;

/**
 * Service Puzzle Captcha Mandiri (Self-Hosted Puzzle Captcha).
 *
 * Diadopsi langsung dari arsitektur SIPBJ (SMKN 1 Majalengka).
 * Zero 3rd party tracker, tanpa Google reCAPTCHA/Cloudflare, aman, mandiri,
 * menggunakan SVG terpotong deterministik dengan enkripsi AES-256-GCM + HMAC-SHA256.
 */
class PuzzleCaptchaService
{
    public const PREFIX = 'pz1';
    public const WIDTH = 640;
    public const HEIGHT = 360;
    public const PIECE_SIZE = 96;
    public const RADIUS = 16;
    public const TOLERANCE = 10; // Rentang toleransi geser piksel
    public const EXPIRY_MINUTES = 5;

    /**
     * Dapatkan kunci HMAC dari konfigurasi aplikasi.
     */
    protected static function getHmacKey(): string
    {
        $key = config('app.key');
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        return hash('sha256', $key . '|puzzle-captcha-hmac');
    }

    /**
     * Dapatkan kunci enkripsi AES-256-GCM (32 bytes).
     */
    protected static function getEncryptionKey(): string
    {
        return hash('sha256', static::getHmacKey() . '|puzzle-captcha-enc', true);
    }

    /**
     * Buat tantangan puzzle baru.
     * Mengembalikan SVG gambar penuh berlubang, SVG potongan, koordinat Y relatif, token HMAC+AES.
     * Koordinat X (jawaban) TIDAK pernah dikirim ke browser.
     *
     * @return array{
     *     token: string,
     *     svg: string,
     *     potonganSvg: string,
     *     y: float,
     *     lebar: int,
     *     tinggi: int,
     *     potongan: int
     * }
     */
    public function challenge(): array
    {
        $maxLimit = self::WIDTH - self::PIECE_SIZE;
        $minX = (int) round($maxLimit * 0.3);
        $x = random_int($minX, $maxLimit);
        $y = random_int((int) round(self::HEIGHT * 0.1), self::HEIGHT - self::PIECE_SIZE);

        $seed = random_int(1, 2147483647);
        $scene = $this->renderScene($seed);

        $mainContent = sprintf(
            '%s<rect x="%d" y="%d" width="%d" height="%d" rx="%d" fill="rgba(11,17,32,0.8)" stroke="rgba(255,255,255,0.55)" stroke-width="3"/>',
            $scene,
            $x,
            $y,
            self::PIECE_SIZE,
            self::PIECE_SIZE,
            self::RADIUS
        );

        $maskId = 'mask_' . bin2hex(random_bytes(4));
        $pieceContent = sprintf(
            '<defs><mask id="%s"><rect x="%d" y="%d" width="%d" height="%d" rx="%d" fill="#fff"/></mask></defs>' .
            '<g mask="url(#%s)">%s</g>' .
            '<rect x="%d" y="%d" width="%d" height="%d" rx="%d" fill="none" stroke="rgba(255,255,255,0.9)" stroke-width="3"/>',
            $maskId,
            $x,
            $y,
            self::PIECE_SIZE,
            self::PIECE_SIZE,
            self::RADIUS,
            $maskId,
            $scene,
            $x,
            $y,
            self::PIECE_SIZE,
            self::PIECE_SIZE,
            self::RADIUS
        );

        $expiresAt = now()->addMinutes(self::EXPIRY_MINUTES)->getTimestampMs();
        $sealedToken = $this->seal(['x' => $x, 'e' => $expiresAt]);
        $signedToken = $sealedToken . '.' . $this->sign($sealedToken);

        return [
            'token' => $signedToken,
            'svg' => $this->wrapSvg($mainContent, self::WIDTH, self::HEIGHT, sprintf('0 0 %d %d', self::WIDTH, self::HEIGHT)),
            'potonganSvg' => $this->wrapSvg($pieceContent, self::PIECE_SIZE, self::PIECE_SIZE, sprintf('%d %d %d %d', $x, $y, self::PIECE_SIZE, self::PIECE_SIZE)),
            'y' => round($y / self::HEIGHT, 4),
            'lebar' => self::WIDTH,
            'tinggi' => self::HEIGHT,
            'potongan' => self::PIECE_SIZE,
        ];
    }

    /**
     * Periksa apakah tebakan posisi pengguna cocok dengan token (tanpa mengonsumsi token).
     */
    public function check(string $payload): bool
    {
        return $this->inspect($payload, false)['valid'];
    }

    /**
     * Verifikasi dan konsumsi token (sekali pakai / anti-replay).
     *
     * @return array{valid: bool, message: string}
     */
    public function verify(string $payload): array
    {
        return $this->inspect($payload, true);
    }

    /**
     * Buat format payload standar klien: pz1.<sealed>.<signature>.<sliderPos>
     */
    public static function formatPayload(string $token, int $sliderPosition): string
    {
        return sprintf('%s.%s.%d', self::PREFIX, $token, $sliderPosition);
    }

    /**
     * Inspeksi payload.
     *
     * @return array{valid: bool, message: string}
     */
    protected function inspect(string $payload, bool $consume): array
    {
        $parts = explode('.', $payload);
        if (count($parts) !== 4 || $parts[0] !== self::PREFIX) {
            return ['valid' => false, 'message' => 'Format verifikasi puzzle tidak sah.'];
        }

        $sealed = $parts[1];
        $signature = $parts[2];
        $position = filter_var($parts[3], FILTER_VALIDATE_INT);

        if ($position === false) {
            return ['valid' => false, 'message' => 'Posisi puzzle tidak valid.'];
        }

        // 1. Verifikasi tanda tangan HMAC konstan
        if (! hash_equals($this->sign($sealed), $signature)) {
            return ['valid' => false, 'message' => 'Tanda tangan verifikasi tidak sah. Silakan muat ulang.'];
        }

        // 2. Dekripsi payload AES-GCM
        $data = $this->unseal($sealed);
        if ($data === null || ! isset($data['x'], $data['e'])) {
            return ['valid' => false, 'message' => 'Data verifikasi gagal didekripsi atau telah rusak.'];
        }

        // 3. Cek masa berlaku token
        $currentMs = now()->getTimestampMs();
        if ($currentMs > $data['e']) {
            return ['valid' => false, 'message' => 'Puzzle verifikasi telah kedaluwarsa. Silakan ulangi puzzle.'];
        }

        // 4. Cek toleransi jarak (selisih posisi x)
        $diff = abs($data['x'] - $position);
        if ($diff > self::TOLERANCE) {
            return ['valid' => false, 'message' => 'Potongan puzzle belum tepat pada posisinya. Silakan geser kembali.'];
        }

        // 5. Konsumsi token (Anti-Replay Guard)
        if ($consume) {
            $cacheKey = 'puzzle_used:' . hash('sha256', $payload);
            $ttlSeconds = max(10, (int) ceil(($data['e'] - $currentMs) / 1000));

            // Jika cache key sudah ada, tolak sebagai pemakaian ulang (replay attack)
            $acquired = Cache::add($cacheKey, 1, $ttlSeconds);
            if (! $acquired) {
                return ['valid' => false, 'message' => 'Puzzle verifikasi ini telah digunakan. Silakan selesaikan puzzle baru.'];
            }
        }

        return ['valid' => true, 'message' => 'Verifikasi berhasil.'];
    }

    /**
     * Tanda tangan HMAC-SHA256 untuk isi token terenkripsi.
     */
    protected function sign(string $sealed): string
    {
        $hash = hash_hmac('sha256', $sealed, static::getHmacKey(), true);

        return $this->base64UrlEncode($hash);
    }

    /**
     * Enkripsi AES-256-GCM untuk objek payload.
     * Format: base64url(iv | auth_tag | ciphertext)
     */
    protected function seal(array $payload): string
    {
        $json = json_encode($payload, JSON_THROW_ON_ERROR);
        $iv = random_bytes(12);
        $tag = '';

        $cipherText = openssl_encrypt(
            $json,
            'aes-256-gcm',
            static::getEncryptionKey(),
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            16
        );

        return $this->base64UrlEncode($iv . $tag . $cipherText);
    }

    /**
     * Buka enkripsi AES-256-GCM.
     */
    protected function unseal(string $sealed): ?array
    {
        try {
            $raw = $this->base64UrlDecode($sealed);
            if (strlen($raw) < 28) { // 12 iv + 16 tag = 28 bytes min
                return null;
            }

            $iv = substr($raw, 0, 12);
            $tag = substr($raw, 12, 16);
            $cipherText = substr($raw, 28);

            $decrypted = openssl_decrypt(
                $cipherText,
                'aes-256-gcm',
                static::getEncryptionKey(),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($decrypted === false) {
                return null;
            }

            return json_decode($decrypted, true, 512, JSON_THROW_ON_ERROR);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * Bangkitkan adegan SVG acak deterministik dari seed.
     */
    protected function renderScene(int $seed): string
    {
        $rng = new class($seed) {
            private int $state;

            public function __construct(int $s)
            {
                $this->state = $s & 0xFFFFFFFF;
            }

            public function next(): float
            {
                $this->state = ($this->state * 1664525 + 1013904223) & 0xFFFFFFFF;

                return ($this->state & 0xFFFFFFFF) / 4294967296.0;
            }

            public function int(int $min, int $range): int
            {
                return $min + (int) floor($this->next() * $range);
            }

            public function color(): string
            {
                $h = $this->int(0, 360);
                $s = $this->int(45, 40);
                $l = $this->int(35, 40);

                return sprintf('hsl(%d, %d%%, %d%%)', $h, $s, $l);
            }
        };

        $color1 = $rng->color();
        $color2 = $rng->color();

        $elements = [];
        $elements[] = sprintf(
            '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="%s"/><stop offset="1" stop-color="%s"/></linearGradient></defs>',
            $color1,
            $color2
        );
        $elements[] = sprintf('<rect width="%d" height="%d" fill="url(#g)"/>', self::WIDTH, self::HEIGHT);

        $shapeCount = $rng->int(26, 14);
        for ($i = 0; $i < $shapeCount; $i++) {
            $cx = $rng->int(0, self::WIDTH);
            $cy = $rng->int(0, self::HEIGHT);
            $r = $rng->int(14, 86);
            $w = $rng->color();
            $type = $rng->int(0, 4);

            switch ($type) {
                case 0:
                    $elements[] = sprintf(
                        '<circle cx="%d" cy="%d" r="%d" fill="none" stroke="%s" stroke-width="%d" opacity="0.85"/>',
                        $cx,
                        $cy,
                        $r,
                        $w,
                        $rng->int(2, 6)
                    );
                    break;
                case 1:
                    $elements[] = sprintf(
                        '<rect x="%d" y="%d" width="%d" height="%d" rx="6" fill="%s" opacity="0.7" transform="rotate(%d %d %d)"/>',
                        $cx,
                        $cy,
                        $r * 2,
                        max(10, $r),
                        $w,
                        $rng->int(-20, 40),
                        $cx,
                        $cy
                    );
                    break;
                case 2:
                    $elements[] = sprintf(
                        '<path d="M%d %d L%d %d L%d %d Z" fill="%s" opacity="0.65"/>',
                        $cx,
                        $cy,
                        $cx + $r,
                        $cy + $rng->int(0, $r),
                        $cx - $rng->int(0, $r),
                        $cy + $r * 2,
                        $w
                    );
                    break;
                default:
                    $elements[] = sprintf(
                        '<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="%s" stroke-width="%d" opacity="0.8"/>',
                        $cx,
                        $cy,
                        $cx + $rng->int(0, 260) - 130,
                        $cy + $rng->int(0, 200) - 100,
                        $w,
                        $rng->int(2, 5)
                    );
                    break;
            }
        }

        return implode('', $elements);
    }

    protected function wrapSvg(string $content, int $width, int $height, string $viewBox): string
    {
        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="%s" width="%d" height="%d" role="img">%s</svg>',
            $viewBox,
            $width,
            $height,
            $content
        );
    }

    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
