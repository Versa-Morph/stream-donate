<?php

namespace App\Http\Controllers;

use App\Services\QrCodeGenerator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObsCanvasController extends Controller
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
     * Halaman editor canvas — hanya untuk streamer yang login.
     *
     * @param Request $request The HTTP request
     * @return View|RedirectResponse The editor view or redirect to setup
     */
    public function editor(Request $request): View|RedirectResponse
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
     *
     * @param Request $request The HTTP request with canvas configuration
     * @return JsonResponse Success/failure response
     */
    public function save(Request $request): JsonResponse
    {
        $streamer = auth()->user()->streamer;

        if (!$streamer) {
            return response()->json(['success' => false, 'message' => 'Streamer not found'], 404);
        }

        $validated = $request->validate([
            'width'                        => 'required|integer|min:320|max:7680',
            'height'                       => 'required|integer|min:180|max:4320',
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
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The canvas view for OBS
     */
    public function render(Request $request, string $slug): View
    {
        $streamer     = $this->findStreamerBySlug($slug);
        $apiKey       = $request->query('key', '');
        $canvasConfig = $streamer->getCanvasConfig();

        // Generate QR SVG untuk widget QR Code using centralized service
        $donateUrl = url('/' . $streamer->slug);
        $qrSvg     = $this->qrGenerator->generate($donateUrl, 200);

        return view('obs.canvas', compact('streamer', 'apiKey', 'canvasConfig', 'donateUrl', 'qrSvg'));
    }
}
