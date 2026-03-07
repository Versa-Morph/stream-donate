<?php

namespace App\Services;

use App\Models\BannedWord;
use Illuminate\Support\Facades\Cache;

class ProfanityFilter
{
    /**
     * Cache TTL in seconds (5 minutes).
     */
    private const CACHE_TTL = 300;

    /**
     * The replacement string shown in place of a banned word.
     */
    private const REPLACEMENT = '***';

    /**
     * Filter banned words out of $text, replacing them with ***.
     *
     * Words are loaded from:
     *   1. Global words (streamer_id = NULL) — applies to everyone.
     *   2. Streamer-specific words (streamer_id = $streamerId) — applies only
     *      to that streamer's donation page.
     *
     * Results are cached per streamer for CACHE_TTL seconds to avoid
     * hitting the database on every donation submission.
     *
     * @param  string   $text       The raw input string.
     * @param  int|null $streamerId The streamer's ID (to include their custom words).
     * @return string               The censored string.
     */
    public function filter(string $text, ?int $streamerId = null): string
    {
        if (trim($text) === '') {
            return $text;
        }

        $words = $this->getWords($streamerId);

        if (empty($words)) {
            return $text;
        }

        return $this->applyFilter($text, $words);
    }

    /**
     * Load the combined word list for this streamer (global + their own),
     * sorted longest-first so multi-word phrases are matched before single
     * words that might be substrings of the phrase.
     *
     * @return string[]
     */
    private function getWords(?int $streamerId): array
    {
        $cacheKey = 'banned_words:' . ($streamerId ?? 'global');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($streamerId) {
            $query = BannedWord::query()->whereNull('streamer_id');

            if ($streamerId !== null) {
                $query->orWhere('streamer_id', $streamerId);
            }

            $words = $query->pluck('word')->toArray();

            // Sort longest first so multi-word phrases ("slot gacor") match
            // before their component single words ("gacor").
            usort($words, fn ($a, $b) => mb_strlen($b) - mb_strlen($a));

            return $words;
        });
    }

    /**
     * Build and apply the regex against $text.
     *
     * Strategy:
     *   - Each word is preg_quote'd to escape special regex characters.
     *   - Words containing spaces are matched as-is (phrase match).
     *   - Single words are wrapped in \b…\b (word boundary) so "gacorkan"
     *     is NOT censored when "gacor" is in the list.
     *   - Flags: i = case-insensitive, u = unicode/multibyte.
     *
     * @param  string   $text
     * @param  string[] $words
     * @return string
     */
    private function applyFilter(string $text, array $words): string
    {
        // Build alternation string, group multi-word and single-word patterns
        $patterns = array_map(function (string $word) {
            $escaped = preg_quote($word, '/');
            // Phrase with spaces — no word boundary needed at internal spaces
            if (str_contains($word, ' ')) {
                return $escaped;
            }
            // Single word — wrap in word boundaries
            return '\\b' . $escaped . '\\b';
        }, $words);

        $regex = '/(' . implode('|', $patterns) . ')/iu';

        return preg_replace($regex, self::REPLACEMENT, $text) ?? $text;
    }
}
