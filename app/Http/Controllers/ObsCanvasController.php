<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ObsCanvasController extends Controller
{
    /**
     * Halaman editor canvas — hanya untuk streamer yang login.
     */
    public function editor(Request $request)
    {
        $streamer = auth()->user()->streamer;

        if (!$streamer) {
            return redirect()->route('streamer.setup');
        }

        $canvasConfig = $streamer->getCanvasConfig();
        $apiKey       = $streamer->api_key;

        return view('streamer.obs_canvas', compact('streamer', 'canvasConfig', 'apiKey'));
    }

    /**
     * Simpan canvas_config ke database — dipanggil via AJAX fetch (JSON).
     */
    public function save(Request $request)
    {
        $streamer = auth()->user()->streamer;

        if (!$streamer) {
            return response()->json(['error' => 'Streamer not found'], 404);
        }

        $validated = $request->validate([
            'width'                        => 'required|integer|in:1920,1280,1366',
            'height'                       => 'required|integer|in:1080,720,768',
            'widgets'                      => 'required|array',
            'widgets.notification'         => 'required|array',
            'widgets.notification.active'  => 'required|boolean',
            'widgets.notification.x'       => 'required|integer|min:0',
            'widgets.notification.y'       => 'required|integer|min:0',
            'widgets.notification.w'       => 'required|integer|min:100',
            'widgets.notification.h'       => 'required|integer|min:60',
            'widgets.leaderboard'          => 'required|array',
            'widgets.leaderboard.active'   => 'required|boolean',
            'widgets.leaderboard.x'        => 'required|integer|min:0',
            'widgets.leaderboard.y'        => 'required|integer|min:0',
            'widgets.leaderboard.w'        => 'required|integer|min:100',
            'widgets.leaderboard.h'        => 'required|integer|min:60',
            'widgets.milestone'            => 'required|array',
            'widgets.milestone.active'     => 'required|boolean',
            'widgets.milestone.x'          => 'required|integer|min:0',
            'widgets.milestone.y'          => 'required|integer|min:0',
            'widgets.milestone.w'          => 'required|integer|min:100',
            'widgets.milestone.h'          => 'required|integer|min:60',
            'widgets.qrcode'               => 'required|array',
            'widgets.qrcode.active'        => 'required|boolean',
            'widgets.qrcode.x'             => 'required|integer|min:0',
            'widgets.qrcode.y'             => 'required|integer|min:0',
            'widgets.qrcode.w'             => 'required|integer|min:80',
            'widgets.qrcode.h'             => 'required|integer|min:80',
        ]);

        $streamer->canvas_config = $validated;
        $streamer->save();

        return response()->json(['success' => true, 'message' => 'Konfigurasi canvas disimpan']);
    }

    /**
     * Render canvas output untuk OBS Browser Source — public, tanpa auth.
     */
    public function render(Request $request, string $slug)
    {
        $streamer     = Streamer::where('slug', $slug)->firstOrFail();
        $apiKey       = $request->query('key', '');
        $canvasConfig = $streamer->getCanvasConfig();

        // Generate QR SVG untuk widget QR Code
        $donateUrl = url('/' . $streamer->slug);
        $qrSvg     = $this->buildQrSvg($donateUrl);

        return view('obs.canvas', compact('streamer', 'apiKey', 'canvasConfig', 'donateUrl', 'qrSvg'));
    }

    /**
     * Generate QR Code SVG (sama seperti QrController).
     */
    private function buildQrSvg(string $url): string
    {
        $raw = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($url);

        $svg = (string) $raw;

        // Inject gradient def + circular logo overlay (sama persis dengan QrController)
        $defs = '<defs>'
            . '<radialGradient id="qrBg" cx="50%" cy="50%" r="50%">'
            . '<stop offset="0%" stop-color="#1a1a2e"/>'
            . '<stop offset="100%" stop-color="#0d0d18"/>'
            . '</radialGradient>'
            . '</defs>';

        $logo = '<circle cx="50%" cy="50%" r="13%" fill="#7c6cfc"/>'
            . '<text x="50%" y="54%" font-family="Inter,sans-serif" font-size="11" font-weight="800" '
            . 'fill="white" text-anchor="middle" dominant-baseline="middle">SD</text>';

        $svg = str_replace('<svg ', '<svg style="background:#141419;border-radius:8px;" ', $svg);
        $svg = str_replace('</svg>', $defs . $logo . '</svg>', $svg);

        return $svg;
    }
}
