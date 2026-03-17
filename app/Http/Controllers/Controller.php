<?php

namespace App\Http\Controllers;

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
}
