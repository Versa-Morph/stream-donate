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
}
