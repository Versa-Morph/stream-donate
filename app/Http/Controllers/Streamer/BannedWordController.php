<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BannedWordController extends Controller
{
    /**
     * Return all banned words for the current streamer's settings page:
     *   - global words (read-only for streamer)
     *   - this streamer's own custom words (can be deleted)
     *
     * Called via AJAX from the settings page.
     */
    public function index(): JsonResponse
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $global = BannedWord::whereNull('streamer_id')
            ->orderBy('word')
            ->pluck('word');

        $custom = BannedWord::where('streamer_id', $streamer->id)
            ->orderBy('word')
            ->get(['id', 'word']);

        return response()->json([
            'global' => $global,
            'custom' => $custom,
        ]);
    }

    /**
     * Add a new custom banned word for this streamer.
     */
    public function store(Request $request): JsonResponse
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        $request->validate([
            'word' => ['required', 'string', 'max:100'],
        ]);

        $word = mb_strtolower(trim($request->input('word')));

        // Already in global list?
        $globalExists = BannedWord::whereNull('streamer_id')
            ->where('word', $word)
            ->exists();

        if ($globalExists) {
            return response()->json([
                'ok'    => false,
                'error' => "Kata \"$word\" sudah ada di daftar global.",
            ], 422);
        }

        // Already in their own list?
        $ownExists = BannedWord::where('streamer_id', $streamer->id)
            ->where('word', $word)
            ->exists();

        if ($ownExists) {
            return response()->json([
                'ok'    => false,
                'error' => "Kata \"$word\" sudah ada di daftar kamu.",
            ], 422);
        }

        $banned = BannedWord::create([
            'word'        => $word,
            'streamer_id' => $streamer->id,
            'created_by'  => Auth::id(),
        ]);

        return response()->json([
            'ok'   => true,
            'id'   => $banned->id,
            'word' => $banned->word,
        ]);
    }

    /**
     * Delete one of this streamer's custom banned words.
     * Streamers cannot delete global words.
     */
    public function destroy(BannedWord $bannedWord): JsonResponse
    {
        $streamer = Auth::user()->streamer;
        abort_unless($streamer, 403);

        // Enforce ownership — streamer can only delete their own words
        if ($bannedWord->streamer_id !== $streamer->id) {
            return response()->json([
                'ok'    => false,
                'error' => 'Kamu tidak bisa menghapus kata ini.',
            ], 403);
        }

        $word = $bannedWord->word;
        $bannedWord->delete();

        return response()->json(['ok' => true, 'word' => $word]);
    }
}
