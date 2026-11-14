<?php

namespace App\Services;

use Composer\InstalledVersions;
use DiceBear\Avatar;
use DiceBear\Style;
use Illuminate\Support\Facades\File;

class AvatarService
{
    /**
     * Kurasi gaya avatar DiceBear yang paling bagus, populer, dan cocok untuk profil
     */
    public const CURATED_STYLES = [
        'lorelei' => [
            'label' => 'Lorelei (Ilustrasi Halus)',
            'description' => 'Gaya ilustrasi potret wajah halus & modern',
        ],
        'avataaars' => [
            'label' => 'Avataaars (Kartun Berkarakter)',
            'description' => 'Karakter kartun ramah dengan aksesoris lengkap',
        ],
        'adventurer' => [
            'label' => 'Adventurer (Fantasi & Petualang)',
            'description' => 'Gaya karakter rpg & ekspresif',
        ],
        'bottts' => [
            'label' => 'Bottts (Robot Ceria)',
            'description' => 'Robot futuristik ramah & geometris',
        ],
        'micah' => [
            'label' => 'Micah (Minimalis Bersih)',
            'description' => 'Desain line-art minimalis kontemporer',
        ],
        'open-peeps' => [
            'label' => 'Open Peeps (Human Hand-Drawn)',
            'description' => 'Sketsa ekspresif ala ilustrasi buku modern',
        ],
        'personas' => [
            'label' => 'Personas (Karakter Kalem)',
            'description' => 'Potret avatar warna lembut & elegan',
        ],
        'shapes' => [
            'label' => 'Shapes (Abstrak Geometris)',
            'description' => 'Kombinasi bentuk geometris artistik',
        ],
        'thumbs' => [
            'label' => 'Thumbs (Emotikon Jempol)',
            'description' => 'Wajah jempol lucu & playful',
        ],
        'pixel-art' => [
            'label' => 'Pixel Art (Retro 8-Bit)',
            'description' => 'Karakter pixel klasik nostalgia',
        ],
    ];

    /**
     * Dapatkan direktori penyimpanan SVG cache statis
     */
    public static function getStorageDir(): string
    {
        $dir = storage_path('app/public/avatars');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }
        return $dir;
    }

    /**
     * Dapatkan daftar gaya avatar yang tersedia
     */
    public static function getStyles(): array
    {
        return self::CURATED_STYLES;
    }

    /**
     * Validasi apakah gaya avatar didukung
     */
    public static function isValidStyle(string $style): bool
    {
        return array_key_exists($style, self::CURATED_STYLES);
    }

    /**
     * Dapatkan URL file SVG statis instan untuk style & seed
     */
    public static function getAvatarUrl(?string $styleName = 'lorelei', ?string $seed = 'default'): string
    {
        $styleName = $styleName ?: 'lorelei';
        if (! self::isValidStyle($styleName)) {
            $styleName = 'lorelei';
        }

        $seed = trim((string) $seed);
        if ($seed === '') {
            $seed = 'DIHADIR';
        }

        $hash = md5($styleName . '_' . $seed);
        $fileName = 'avatar_' . $styleName . '_' . $hash . '.svg';
        $filePath = self::getStorageDir() . DIRECTORY_SEPARATOR . $fileName;

        if (! File::exists($filePath)) {
            self::renderAndSaveSvg($styleName, $seed, $filePath);
        }

        return asset('storage/avatars/' . $fileName);
    }

    /**
     * Generate SVG DiceBear dengan disk caching otomatis
     */
    public static function renderSvg(?string $styleName = 'lorelei', ?string $seed = 'default', array $options = []): string
    {
        $styleName = $styleName ?: 'lorelei';
        if (! self::isValidStyle($styleName)) {
            $styleName = 'lorelei';
        }

        $seed = trim((string) $seed);
        if ($seed === '') {
            $seed = 'DIHADIR';
        }

        $hash = md5($styleName . '_' . $seed . '_' . serialize($options));
        $fileName = 'avatar_' . $styleName . '_' . $hash . '.svg';
        $filePath = self::getStorageDir() . DIRECTORY_SEPARATOR . $fileName;

        if (File::exists($filePath)) {
            return File::get($filePath);
        }

        return self::renderAndSaveSvg($styleName, $seed, $filePath, $options);
    }

    /**
     * Batas jumlah berkas avatar yang boleh tersimpan di disk.
     * Mencegah pembanjiran penyimpanan dari kombinasi style/seed tak terbatas.
     */
    private const MAX_CACHED_FILES = 3000;

    /**
     * Status kapasitas cache avatar pada request berjalan.
     */
    private static ?bool $cacheWritable = null;

    /**
     * Apakah direktori cache masih boleh ditulisi.
     */
    private static function cacheWritable(): bool
    {
        if (self::$cacheWritable !== null) {
            return self::$cacheWritable;
        }

        try {
            $count = count(File::files(self::getStorageDir()));
            self::$cacheWritable = $count < self::MAX_CACHED_FILES;
        } catch (\Throwable) {
            self::$cacheWritable = false;
        }

        return self::$cacheWritable;
    }

    /**
     * Render SVG via DiceBear dan simpan ke file statis bila cache masih tersedia.
     */
    private static function renderAndSaveSvg(string $styleName, string $seed, string $filePath, array $options = []): string
    {
        try {
            $basePath = InstalledVersions::getInstallPath('dicebear/styles');
            $stylePath = $basePath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $styleName . '.json';

            if (! File::exists($stylePath)) {
                $svg = self::fallbackSvg($seed);
            } else {
                $style = Style::fromJson(File::get($stylePath));
                $avatar = new Avatar($style, array_merge([
                    'seed' => $seed,
                ], $options));

                $svg = (string) $avatar;
            }
        } catch (\Throwable $e) {
            report($e);
            $svg = self::fallbackSvg($seed);
        }

        // Selalu kembalikan SVG yang valid meskipun penulisan cache ditolak.
        if (self::cacheWritable()) {
            File::put($filePath, $svg);
        }

        return $svg;
    }

    /**
     * Prewarm atau generate seluruh ikon preview gaya agar instan
     */
    public static function prewarmPreviews(): void
    {
        foreach (array_keys(self::CURATED_STYLES) as $style) {
            self::renderSvg($style, 'preview');
        }
    }

    /**
     * Fallback SVG bila terjadi anomali
     */
    private static function fallbackSvg(string $seed): string
    {
        $initial = mb_strtoupper(mb_substr($seed, 0, 1));
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100%" height="100%">'
            . '<rect width="100" height="100" rx="20" fill="#3B82F6"/>'
            . '<text x="50" y="58" font-family="Inter, sans-serif" font-size="42" font-weight="700" fill="#FFFFFF" text-anchor="middle" dominant-baseline="middle">' . htmlspecialchars($initial) . '</text>'
            . '</svg>';
    }
}
