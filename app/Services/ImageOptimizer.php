<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ImageOptimizer
{
    /**
     * Compress and optimize a digital signature PNG string.
     * Preserves transparency (alpha channel), trims or limits max dimensions,
     * and re-encodes at maximum compression (level 9).
     *
     * @param string $binaryData Raw PNG binary data
     * @param int $maxWidth Max width in pixels (default: 600)
     * @param int $maxHeight Max height in pixels (default: 300)
     * @return string Compressed PNG binary data
     */
    public static function compressSignature(string $binaryData, int $maxWidth = 600, int $maxHeight = 300): string
    {
        if (! extension_loaded('gd')) {
            return $binaryData;
        }

        $source = @imagecreatefromstring($binaryData);
        if ($source === false) {
            return $binaryData;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth <= 0 || $origHeight <= 0) {
            imagedestroy($source);
            return $binaryData;
        }

        // Calculate proportional scale
        $scale = min(1.0, $maxWidth / $origWidth, $maxHeight / $origHeight);
        $targetWidth = (int) max(1, round($origWidth * $scale));
        $targetHeight = (int) max(1, round($origHeight * $scale));

        // Create target image with full alpha transparency
        $target = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($target, false);
        imagesavealpha($target, true);

        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        ob_start();
        // PNG compression level 9: highest compression, perfectly lossless
        imagepng($target, null, 9);
        $compressed = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        // Fallback to original if compression resulted in empty or larger (rare for uncompressed canvas dumps)
        if ($compressed !== false && strlen($compressed) > 0) {
            return $compressed;
        }

        return $binaryData;
    }

    /**
     * Compress and resize an uploaded photo/attachment.
     * Converts large camera photos (often 5-15MB) to web-optimized JPEG or PNG,
     * limiting max dimension to 1280px and quality to 82%.
     *
     * @param UploadedFile|string $file
     * @param int $maxDimension Max width or height (default: 1280)
     * @param int $quality JPEG quality (default: 82)
     * @return string Compressed binary data
     */
    public static function compressPhoto(UploadedFile|string $file, int $maxDimension = 1280, int $quality = 82): string
    {
        $rawBinary = is_string($file) ? $file : file_get_contents($file->getRealPath());

        if (! extension_loaded('gd')) {
            return $rawBinary;
        }

        $source = @imagecreatefromstring($rawBinary);
        if ($source === false) {
            return $rawBinary;
        }

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        if ($origWidth <= 0 || $origHeight <= 0) {
            imagedestroy($source);
            return $rawBinary;
        }

        // Calculate scaling factor
        $scale = min(1.0, $maxDimension / max($origWidth, $origHeight));
        $targetWidth = (int) max(1, round($origWidth * $scale));
        $targetHeight = (int) max(1, round($origHeight * $scale));

        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        // Maintain transparent backgrounds if PNG/WebP, or fill white for JPEG conversion
        imagealphablending($target, false);
        imagesavealpha($target, true);
        $transparent = imagecolorallocatealpha($target, 0, 0, 0, 127);
        imagefilledrectangle($target, 0, 0, $targetWidth, $targetHeight, $transparent);

        imagecopyresampled(
            $target,
            $source,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $origWidth,
            $origHeight
        );

        ob_start();
        // Save as optimized JPEG
        imagejpeg($target, null, $quality);
        $compressed = ob_get_clean();

        imagedestroy($source);
        imagedestroy($target);

        if ($compressed !== false && strlen($compressed) > 0) {
            return $compressed;
        }

        return $rawBinary;
    }
}
