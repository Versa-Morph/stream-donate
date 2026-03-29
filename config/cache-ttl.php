<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live) Settings
    |--------------------------------------------------------------------------
    |
    | Cache duration settings in seconds for various features.
    | Adjust these values based on your application's performance needs.
    |
    */

    /**
     * Streamer stats cache duration (in seconds)
     * Used for: buildStats() method in Streamer model when activity is recent
     * Default: 15 seconds (real-time-ish updates for active streams)
     */
    'streamer_stats_ttl' => env('CACHE_STREAMER_STATS_TTL', 15),

    /**
     * Streamer stats cache for recent activity (in seconds)
     * Used when last donation was 5-30 minutes ago
     * Default: 60 seconds (1 minute)
     */
    'streamer_stats_recent' => env('CACHE_STREAMER_STATS_RECENT', 60),

    /**
     * Streamer stats cache for idle streamers (in seconds)
     * Used when last donation was 30-120 minutes ago
     * Default: 180 seconds (3 minutes)
     */
    'streamer_stats_idle' => env('CACHE_STREAMER_STATS_IDLE', 180),

    /**
     * Streamer stats cache for inactive streamers (in seconds)
     * Used when no donations in last 2 hours or no donations at all
     * Default: 300 seconds (5 minutes)
     */
    'streamer_stats_inactive' => env('CACHE_STREAMER_STATS_INACTIVE', 300),

    /**
     * Profanity filter cache duration (in seconds)
     * Used for: ProfanityFilter service
     * Default: 300 seconds (5 minutes)
     */
    'profanity_filter_ttl' => env('CACHE_PROFANITY_FILTER_TTL', 300),

    /**
     * Widget settings cache duration (in seconds)
     * Used for: OBS widget configurations
     * Default: 60 seconds (1 minute)
     */
    'widget_settings_ttl' => env('CACHE_WIDGET_SETTINGS_TTL', 60),
];
