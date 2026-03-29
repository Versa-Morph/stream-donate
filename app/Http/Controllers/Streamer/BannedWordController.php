<?php

namespace App\Http\Controllers\Streamer;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $streamer = auth()->user()->streamer;
        abort_unless($streamer, 403);

        // PERFORMANCE: Limit global words display (unlikely to exceed, but safe)
        $global = BannedWord::whereNull('streamer_id')
            ->orderBy('word')
            ->limit(config('pagination.max_banned_words', 1000))
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
        $streamer = auth()->user()->streamer;
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
                'success' => false,
                'message' => "Kata \"$word\" sudah ada di daftar global.",
            ], 422);
        }

        // Already in their own list?
        $ownExists = BannedWord::where('streamer_id', $streamer->id)
            ->where('word', $word)
            ->exists();

        if ($ownExists) {
            return response()->json([
                'success' => false,
                'message' => "Kata \"$word\" sudah ada di daftar kamu.",
            ], 422);
        }

        $banned = BannedWord::create([
            'word'        => $word,
            'streamer_id' => $streamer->id,
            'created_by'  => Auth::id(),
        ]);

        // Invalidate profanity filter cache for this streamer
        $this->invalidateProfanityCache($streamer->id);

        return response()->json([
            'success' => true,
            'message' => 'Kata berhasil ditambahkan.',
            'data'    => [
                'id'   => $banned->id,
                'word' => $banned->word,
            ],
        ]);
    }

    /**
     * Delete one of this streamer's custom banned words.
     * Streamers cannot delete global words.
     */
    public function destroy(BannedWord $bannedWord): JsonResponse
    {
        $streamer = auth()->user()->streamer;
        abort_unless($streamer, 403);

        // Enforce ownership — streamer can only delete their own words
        if ($bannedWord->streamer_id !== $streamer->id) {
            return response()->json([
                'success' => false,
                'message' => 'Kamu tidak bisa menghapus kata ini.',
            ], 403);
        }

        $word = $bannedWord->word;
        $bannedWord->delete();

        // Invalidate profanity filter cache for this streamer
        $this->invalidateProfanityCache($streamer->id);

        return response()->json([
            'success' => true,
            'message' => 'Kata berhasil dihapus.',
            'data'    => ['word' => $word],
        ]);
    }

    /**
     * Invalidate profanity filter cache when banned words are modified.
     *
     * @param int $streamerId The streamer whose cache should be invalidated
     */
    private function invalidateProfanityCache(int $streamerId): void
    {
        Cache::forget("banned_words:{$streamerId}");
        Cache::forget("banned_words:global");
    }
}
