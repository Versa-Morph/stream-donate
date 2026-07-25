<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for One-Time Password (OTP) verification.
    |
    */

    /**
     * OTP code length (number of characters).
     */
    'length' => (int) env('OTP_LENGTH', 8),

    /**
     * OTP expiration time in minutes.
     */
    'expires_minutes' => (int) env('OTP_EXPIRES_MINUTES', 5),

    /**
     * Maximum OTP verification attempts before lockout.
     */
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 3),

    /**
     * Allowed characters for OTP generation.
     * Excludes similar-looking characters (I, O, 0, 1) for clarity.
     */
    'characters' => 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789',
];
