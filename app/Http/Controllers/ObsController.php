<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObsController extends Controller
{
    /**
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * SECURITY NOTE: OBS Widget API Key Policy
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     * 
     * OBS widgets (overlay, leaderboard, milestone, subathon, etc.) accept an
     * API key via query parameter but DO NOT validate it for the following reasons:
     * 
     * 1. INTENTIONALLY PUBLIC:
     *    - These widgets are displayed on the streamer's public livestream
     *    - Thousands of viewers can see the widgets in real-time
     *    - The data shown (donations, leaderboards) is public information
     *    - Requiring API key validation provides no security benefit
     * 
     * 2. PROTECTED DATA SOURCE:
     *    - The actual sensitive operation (SSE stream) DOES validate API keys
     *    - SSE endpoint at /{slug}/sse requires valid API key (hash_equals check)
     *    - Widgets only display data; they don't modify anything
     * 
     * 3. USER EXPERIENCE:
     *    - Easy widget setup: just add slug to URL
     *    - No risk of API key exposure in OBS (URLs visible in Scene Collection)
     *    - Streamers can share widget URLs without security concerns
     * 
     * This is a deliberate design decision, not a security oversight.
     * ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
     */

    /**
     * Overlay alert widget untuk OBS.
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The overlay widget view
     */
    public function overlay(Request $request, string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);

        // Validasi API key opsional (bisa diakses tanpa key tapi hanya baca)
        $apiKey = $request->query('key', '');

        return view('obs.overlay', compact('streamer', 'apiKey'));
    }

    /**
     * Leaderboard widget untuk OBS.
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The leaderboard widget view
     */
    public function leaderboard(Request $request, string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);
        $apiKey = $request->query('key', '');

        return view('obs.leaderboard', compact('streamer', 'apiKey'));
    }

    /**
     * Milestone progress bar widget untuk OBS.
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The milestone widget view
     */
    public function milestone(Request $request, string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);
        $apiKey = $request->query('key', '');

        return view('obs.milestone', compact('streamer', 'apiKey'));
    }

    /**
     * Subathon timer widget untuk OBS.
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The subathon timer widget view
     */
    public function subathon(Request $request, string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);
        $apiKey = $request->query('key', '');

        $widgetSettings = $streamer->getWidgetSettings();
        $widget = $widgetSettings['subathon'] ?? [];

        return view('obs.subathon', compact('streamer', 'apiKey', 'widget'));
    }

    /**
     * Running text widget untuk OBS.
     *
     * Displays recent donation messages in a scrolling marquee format.
     *
     * @param Request $request The HTTP request
     * @param string $slug Streamer's unique slug
     * @return View The running text widget view
     */
    public function runningText(Request $request, string $slug): View
    {
        $streamer = $this->findStreamerBySlug($slug);
        $apiKey = $request->query('key', '');

        $widgetSettings = $streamer->getWidgetSettings();
        $widget = $widgetSettings['running_text'] ?? [];

        $streamerMessage = $widget['text'] ?? 'Terima kasih atas donasi Anda! Semangat terus streamnya!';
        
        $donations = $streamer->donations()
            ->whereNotNull('message')
            ->where('message', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit(config('pagination.running_text_donations', 20))
            ->get();

        return view('obs.running_text', compact('streamer', 'apiKey', 'widget', 'streamerMessage', 'donations'));
    }
}
