<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Alert Duration Tiers
    |--------------------------------------------------------------------------
    |
    | Default tier structure for alert duration based on donation amount.
    | Each tier specifies a minimum amount ('from') and duration in seconds.
    | Streamers can customize these tiers in their dashboard.
    |
    */
    'default_tiers' => [
        ['from' => 0, 'duration' => 5],
        ['from' => 10000, 'duration' => 8],
        ['from' => 50000, 'duration' => 12],
        ['from' => 100000, 'duration' => 20],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Notification Sound
    |--------------------------------------------------------------------------
    |
    | Default notification sound played for donation alerts.
    | Streamers can upload custom sounds or select from presets.
    |
    */
    'default_sound' => 'ding',

    /*
    |--------------------------------------------------------------------------
    | Available Sound Presets
    |--------------------------------------------------------------------------
    |
    | List of available preset notification sounds.
    | These must have corresponding audio files in the public assets.
    |
    */
    'sound_presets' => [
        'ding', 'coin', 'whoosh', 'chime', 'pop', 'tada',
        'woosh_light', 'blip', 'sparkle', 'fanfare',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alert Queue TTL
    |--------------------------------------------------------------------------
    |
    | Time to live for alert queue entries in minutes.
    | After this time, alerts are automatically cleaned up by the job.
    |
    */
    'queue_ttl_minutes' => 5,

    /*
    |--------------------------------------------------------------------------
    | Subathon Default Settings
    |--------------------------------------------------------------------------
    |
    | Default configuration for subathon timer feature.
    |
    */
    'subathon' => [
        'default_start_minutes' => 60, // 1 hour
        'default_additional_values' => [
            ['from' => 0, 'minutes' => 1],
            ['from' => 10000, 'minutes' => 2],
            ['from' => 50000, 'minutes' => 5],
            ['from' => 100000, 'minutes' => 10],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Test Alert Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Additional protection for test alerts beyond middleware throttle.
    | Limits how many test alerts can be created in a time window.
    |
    */
    'test_alert' => [
        'max_per_window' => 20,    // Maximum test alerts per window
        'window_minutes' => 5,     // Time window in minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Widget Settings Validation
    |--------------------------------------------------------------------------
    |
    | Validation constraints for widget settings values.
    |
    */
    'widget_validation' => [
        'max_string_length' => 255,           // Max length for text fields
        'max_url_length' => 2048,             // Max length for URL fields
        'max_numeric_value' => 999999,        // Max value for numeric fields
        'allowed_color_pattern' => '/^#[0-9A-Fa-f]{6}$/', // Hex color format
    ],
];
