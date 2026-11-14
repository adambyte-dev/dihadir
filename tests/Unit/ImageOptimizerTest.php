<?php

namespace Tests\Unit;

use App\Services\ImageOptimizer;
use Tests\TestCase;

class ImageOptimizerTest extends TestCase
{
    public function test_compress_signature_preserves_png_and_compresses(): void
    {
        $im = imagecreatetruecolor(800, 400);
        imagesavealpha($im, true);
        $transparent = imagecolorallocatealpha($im, 0, 0, 0, 127);
        imagefill($im, 0, 0, $transparent);
        $black = imagecolorallocate($im, 15, 23, 42);
        imageline($im, 10, 10, 790, 390, $black);

        ob_start();
        imagepng($im, null, 1); // low compression
        $rawPng = ob_get_clean();
        imagedestroy($im);

        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $rawPng);

        $compressed = ImageOptimizer::compressSignature($rawPng, 400, 200);

        $this->assertNotEmpty($compressed);
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $compressed);

        // Verify dimensions were scaled
        $info = getimagesizefromstring($compressed);
        $this->assertLessThanOrEqual(400, $info[0]);
        $this->assertLessThanOrEqual(200, $info[1]);
    }

    public function test_compress_photo_converts_to_optimized_jpeg(): void
    {
        $im = imagecreatetruecolor(1600, 1200);
        $red = imagecolorallocate($im, 200, 50, 50);
        imagefilledrectangle($im, 0, 0, 1600, 1200, $red);

        ob_start();
        imagepng($im, null, 0); // uncompressed
        $rawPng = ob_get_clean();
        imagedestroy($im);

        $compressed = ImageOptimizer::compressPhoto($rawPng, 800, 80);

        $this->assertNotEmpty($compressed);

        $info = getimagesizefromstring($compressed);
        $this->assertLessThanOrEqual(800, $info[0]);
        $this->assertLessThanOrEqual(800, $info[1]);
        $this->assertEquals('image/jpeg', $info['mime']);
    }
}
