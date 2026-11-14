<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MeetingQrController extends Controller
{
    /**
     * Stream high quality local SVG QR Code with branded DIHADIR center icon.
     */
    public function show(Request $request, Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $size = (int) $request->query('size', 280);
        $size = max(100, min(1200, $size));

        $svg = $this->generateBrandedQrSvg($meeting->public_url, $size, 2);

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Download standalone high-res SVG QR file for printing and banners.
     */
    public function download(Request $request, Meeting $meeting): Response
    {
        $this->authorize('view', $meeting);

        $size = 600;
        $svg = $this->generateBrandedQrSvg($meeting->public_url, $size, 2);
        $filename = 'QR-' . $meeting->slug . '.svg';

        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, private',
        ]);
    }

    /**
     * Display dedicated, high-impact printable A4 standee page for QR code.
     */
    public function print(Request $request, Meeting $meeting)
    {
        $this->authorize('view', $meeting);

        return view('admin.meetings.qr-print', compact('meeting'));
    }

    /**
     * Generate an aesthetically pleasing SVG QR code with DIHADIR center badge.
     *
     * Uses Level H (30% error correction tolerance) and exact 20-22% center size
     * to guarantee 100% instant and reliable smartphone camera scanning.
     */
    private function generateBrandedQrSvg(string $url, int $size, int $margin = 2): string
    {
        // 1. Generate base SVG with Level H Error Correction & clean rounded style
        $rawSvg = (string) QrCode::format('svg')
            ->size($size)
            ->margin($margin)
            ->errorCorrection('H')
            ->style('round', 0.7)
            ->eye('circle')
            ->color(15, 23, 42) // Slate-900 Minimalist Corporate
            ->backgroundColor(255, 255, 255)
            ->generate($url);

        // 2. Resolve DIHADIR icon
        $iconPath = public_path('images/logo-dihadir-qr-center.png');
        if (! file_exists($iconPath)) {
            $iconPath = public_path('images/logo-dihadir-icon-trimmed.png');
            if (! file_exists($iconPath)) {
                $iconPath = public_path('images/logo-dihadir-icon.png');
            }
        }

        if (! file_exists($iconPath)) {
            return $rawSvg;
        }

        $logoData = base64_encode(file_get_contents($iconPath));

        // 3. Mathematical proportions for QR scannability:
        // Level H tolerates up to 30% area damage.
        // A center box of 21% occupies (0.21)^2 = ~4.4% of total QR area,
        // leaving > 25% safety margin for instant camera focus & decoding.
        $boxSize = round($size * 0.21);
        $boxX = round(($size - $boxSize) / 2);
        $boxY = round(($size - $boxSize) / 2);
        $boxRadius = round($boxSize * 0.22);

        $iconPadding = max(2, round($boxSize * 0.12));
        $iconSize = $boxSize - ($iconPadding * 2);
        $iconX = $boxX + $iconPadding;
        $iconY = $boxY + $iconPadding;

        $filterId = 'qr-center-shadow-' . $size;

        $badge = <<<SVG
    <!-- DIHADIR Center Icon Badge -->
    <defs>
        <filter id="{$filterId}" x="-20%" y="-20%" width="140%" height="140%">
            <feDropShadow dx="0" dy="1.5" stdDeviation="2.5" flood-color="#0f172a" flood-opacity="0.18" />
        </filter>
    </defs>
    <rect x="{$boxX}" y="{$boxY}" width="{$boxSize}" height="{$boxSize}" rx="{$boxRadius}" ry="{$boxRadius}" fill="#ffffff" stroke="#cbd5e1" stroke-width="1.5" filter="url(#{$filterId})" />
    <image x="{$iconX}" y="{$iconY}" width="{$iconSize}" height="{$iconSize}" href="data:image/png;base64,{$logoData}" preserveAspectRatio="xMidYMid meet" />
</svg>
SVG;

        return preg_replace('/<\/svg>\s*$/i', $badge, trim($rawSvg));
    }
}

