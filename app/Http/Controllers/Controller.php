<?php

namespace App\Http\Controllers;

use App\Models\Streamer;

abstract class Controller
{
    /**
     * Escape LIKE wildcards in search strings to prevent slow query attacks.
     * 
     * Without escaping, a user could input "%" or "_" characters that have
     * special meaning in SQL LIKE clauses, potentially causing:
     * - Unexpected search results
     * - Slow queries (ReDoS-style database attacks)
     * 
     * @param string $value The user-provided search string
     * @return string The escaped string safe for use in LIKE queries
     */
    protected function escapeLikeWildcards(string $value): string
    {
        return str_replace(
            ['\\', '%', '_'],
            ['\\\\', '\\%', '\\_'],
            $value
        );
    }

    /**
     * Find a streamer by slug or fail with 404.
     * 
     * This centralizes the repeated pattern of looking up streamers by slug
     * across multiple controllers, ensuring consistent error handling and
     * reducing code duplication.
     * 
     * @param string $slug The streamer's unique slug
     * @return Streamer The found streamer instance
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException if not found
     */
    protected function findStreamerBySlug(string $slug): Streamer
    {
        return Streamer::where('slug', $slug)->firstOrFail();
    }
}
