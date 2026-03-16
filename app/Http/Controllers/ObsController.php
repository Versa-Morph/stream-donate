<?php

namespace App\Http\Controllers;

use App\Models\Streamer;
use Illuminate\Http\Request;

class ObsController extends Controller
{
    /**
     * Overlay alert widget untuk OBS
     */
    public function overlay(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        // Validasi API key opsional (bisa diakses tanpa key tapi hanya baca)
        $apiKey = $request->query('key', '');

        return view('obs.overlay', compact('streamer', 'apiKey'));
    }

    /**
     * Leaderboard widget untuk OBS
     */
    public function leaderboard(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();
        $apiKey = $request->query('key', '');

        return view('obs.leaderboard', compact('streamer', 'apiKey'));
    }

    /**
     * Milestone progress bar widget untuk OBS
     */
    public function milestone(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();
        $apiKey = $request->query('key', '');

        return view('obs.milestone', compact('streamer', 'apiKey'));
    }

    /**
     * Subathon timer widget untuk OBS
     */
    public function subathon(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();
        $apiKey = $request->query('key', '');

        $widgetSettings = $streamer->getWidgetSettings();
        $widget = $widgetSettings['subathon'] ?? [];

        return view('obs.subathon', compact('streamer', 'apiKey', 'widget'));
    }

    /**
     * Running text widget untuk OBS
     */
    public function runningText(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();
        $apiKey = $request->query('key', '');

        $widgetSettings = $streamer->getWidgetSettings();
        $widget = $widgetSettings['running_text'] ?? [];

        $streamerMessage = $widget['text'] ?? 'Terima kasih atas donasi Anda! Semangat terus streamnya!';
        
        $donations = $streamer->donations()
            ->whereNotNull('message')
            ->where('message', '!=', '')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('obs.running_text', compact('streamer', 'apiKey', 'widget', 'streamerMessage', 'donations'));
    }
}
