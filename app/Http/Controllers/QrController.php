<?php

namespace App\Http\Controllers;

use App\Services\QrCodeGenerator;
use Illuminate\Http\Response;
use Illuminate\View\View;

class QrController extends Controller
{
    /**
     * QR Code Generator service instance.
     */
    private QrCodeGenerator $qrGenerator;

    /**
     * Create a new controller instance.
     */
    public function __construct(QrCodeGenerator $qrGenerator)
    {
        $this->qrGenerator = $qrGenerator;
    }

    /**
     * Generate QR code SVG untuk form donasi streamer.
     * 
     * Route: GET /{slug}/qr
     * Returns inline SVG with StreamDonate logo overlay.
     *
     * @param string $slug Streamer's unique slug
     * @return Response SVG image response with caching headers
     */
    public function show(string $slug): Response
    {
        $streamer = $this->findStreamerBySlug($slug);
        $donateUrl = url("/{$slug}");

        $svg = $this->qrGenerator->generate($donateUrl);

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=' . config('export.qr_cache_ttl', 3600),
        ]);
    }

    /**
     * OBS Widget — halaman full transparent untuk Browser Source OBS.
     * 
     * Route: GET /{slug}/obs/qr
     *
     * @param string $slug Streamer's unique slug
     * @return View The OBS QR widget view
     */
    public function obsWidget(string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);
        $donateUrl = url("/{$slug}");
        $qrSvg = $this->qrGenerator->generate($donateUrl, 360);

        return view('obs.qr', compact('streamer', 'donateUrl', 'qrSvg'));
    }
}
