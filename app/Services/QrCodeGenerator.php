<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

/**
 * QR Code Generator Service
 *
 * Generates QR codes with StreamDonate branding (logo overlay).
 * This service centralizes QR generation logic used by multiple controllers.
 *
 * Features:
 * - High error correction (H level) for logo overlay compatibility
 * - Branded logo overlay with gradient and shadow
 * - Customizable size
 * - Dark theme colors matching StreamDonate design
 * - Graceful error handling with fallback
 */
class QrCodeGenerator
{
    /**
     * Default QR code size in pixels.
     */
    private const DEFAULT_SIZE = 300;

    /**
     * QR code foreground color (matches --text: #f1f1f6).
     */
    private const FG_COLOR = [241, 241, 246];

    /**
     * QR code background color (matches --surface: #141419).
     */
    private const BG_COLOR = [20, 20, 25];

    /**
     * Logo size as percentage of QR code size.
     */
    private const LOGO_SIZE_RATIO = 0.09;

    /**
     * Generate QR code SVG with StreamDonate logo overlay.
     *
     * Uses error correction level H (highest, 30% redundancy) to ensure
     * QR code remains scannable despite logo covering the center.
     *
     * @param string $url The URL to encode in the QR code
     * @param int $size QR code size in pixels (default: 300)
     * @return string Complete SVG markup with embedded logo
     * @throws \App\Exceptions\QrGenerationException When QR generation fails and no fallback possible
     */
    public function generate(string $url, int $size = self::DEFAULT_SIZE): string
    {
        try {
            $rawSvg = $this->generateBaseQr($url, $size);
            $logoSvg = $this->buildLogoOverlay($size);

            return str_replace('</svg>', $logoSvg . "\n</svg>", $rawSvg);
        } catch (\Throwable $e) {
            Log::error('QrCodeGenerator: Failed to generate QR code', [
                'url'   => $url,
                'size'  => $size,
                'error' => $e->getMessage(),
            ]);

            // Return a fallback SVG with error message
            return $this->buildFallbackSvg($size);
        }
    }

    /**
     * Generate the base QR code SVG without logo.
     *
     * @param string $url URL to encode
     * @param int $size Size in pixels
     * @return string Raw SVG markup
     */
    private function generateBaseQr(string $url, int $size): string
    {
        return (string) QrCode::format('svg')
            ->size($size)
            ->errorCorrection('H')
            ->color(...self::FG_COLOR)
            ->backgroundColor(...self::BG_COLOR)
            ->margin(1)
            ->generate($url);
    }

    /**
     * Build the logo overlay SVG markup.
     *
     * Creates a circular logo with:
     * - Radial gradient (purple theme)
     * - Drop shadow filter
     * - "SD" text centered
     *
     * @param int $qrSize The QR code size (used to calculate logo dimensions)
     * @return string SVG markup for the logo overlay
     */
    private function buildLogoOverlay(int $qrSize): string
    {
        $logoR = round($qrSize * self::LOGO_SIZE_RATIO);
        $cx = round($qrSize / 2);
        $cy = round($qrSize / 2);
        $fontSize = round($logoR * 0.72);

        return <<<SVG

  <!-- StreamDonate Logo Overlay -->
  <defs>
    <radialGradient id="sdGrad" cx="50%" cy="30%" r="70%">
      <stop offset="0%"   stop-color="#a99dff"/>
      <stop offset="100%" stop-color="#6356e8"/>
    </radialGradient>
    <filter id="sdShadow" x="-30%" y="-30%" width="160%" height="160%">
      <feDropShadow dx="0" dy="0" stdDeviation="3" flood-color="rgba(0,0,0,0.6)"/>
    </filter>
  </defs>
  <circle cx="{$cx}" cy="{$cy}" r="{$logoR}" fill="url(#sdGrad)" filter="url(#sdShadow)"/>
  <text
    x="{$cx}" y="{$cy}"
    dominant-baseline="central"
    text-anchor="middle"
    font-family="Inter, Arial, sans-serif"
    font-size="{$fontSize}"
    font-weight="800"
    fill="#ffffff"
    letter-spacing="-1"
  >SD</text>
SVG;
    }

    /**
     * Build a fallback SVG when QR generation fails.
     *
     * Displays a placeholder with error indication for graceful degradation.
     *
     * @param int $size SVG dimensions
     * @return string Fallback SVG markup
     */
    private function buildFallbackSvg(int $size): string
    {
        $cx = round($size / 2);
        $cy = round($size / 2);
        $iconSize = round($size * 0.15);
        $textY = round($cy + $iconSize + 20);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$size}" height="{$size}" viewBox="0 0 {$size} {$size}">
  <rect width="{$size}" height="{$size}" fill="#141419"/>
  <text x="{$cx}" y="{$cy}" dominant-baseline="central" text-anchor="middle" font-size="{$iconSize}" fill="#6356e8">⚠</text>
  <text x="{$cx}" y="{$textY}" dominant-baseline="central" text-anchor="middle" font-family="Inter, Arial, sans-serif" font-size="14" fill="#888">QR Unavailable</text>
</svg>
SVG;
    }
}
