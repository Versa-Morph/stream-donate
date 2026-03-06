<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    /**
     * Generate QR code SVG untuk form donasi streamer.
     * Route: GET /{slug}/qr
     * Returns inline SVG with StreamDonate logo overlay.
     */
    public function show(string $slug): Response
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        $donateUrl = url("/{$slug}");

        $svg = $this->buildQrSvg($donateUrl, $streamer->display_name);

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * OBS Widget — halaman full transparent untuk Browser Source OBS.
     * Route: GET /{slug}/obs/qr
     */
    public function obsWidget(string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();
        $donateUrl = url("/{$slug}");
        $qrSvg = $this->buildQrSvg($donateUrl, $streamer->display_name, size: 360);

        return view('obs.qr', compact('streamer', 'donateUrl', 'qrSvg'));
    }

    /**
     * Build QR SVG dengan logo StreamDonate di tengah.
     *
     * Strategi:
     * 1. Generate QR via BaconQrCode (SVG renderer) dengan error correction H
     *    sehingga QR masih terbaca meski ~30% data tertutup logo.
     * 2. Parse SVG output, inject <defs> gradient + lingkaran logo SD di tengah.
     */
    private function buildQrSvg(string $url, string $name, int $size = 300): string
    {
        // Generate QR SVG — error correction H = tertinggi (30% redundancy)
        $rawSvg = (string) QrCode::format('svg')
            ->size($size)
            ->errorCorrection('H')
            ->color(241, 241, 246)        // --text: #f1f1f6
            ->backgroundColor(20, 20, 25) // --surface: #141419
            ->margin(1)
            ->generate($url);

        // Ukuran logo = 18% dari size QR, diposisikan tepat di tengah
        $logoR    = round($size * 0.09);   // radius lingkaran logo
        $logoSize = $logoR * 2;
        $cx       = round($size / 2);
        $cy       = round($size / 2);
        $fontSize = round($logoR * 0.72);

        // Inject defs + logo overlay sebelum </svg>
        $logoSvg = <<<SVG

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

        // Insert logo sebelum tag penutup </svg>
        $svg = str_replace('</svg>', $logoSvg . "\n</svg>", $rawSvg);

        return $svg;
    }
}
