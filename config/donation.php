<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Donation Amount
    |--------------------------------------------------------------------------
    |
    | Maximum donation amount in IDR (Indonesian Rupiah).
    | Default: 100,000,000 (100 million rupiah)
    | 
    | This limit prevents abuse and ensures donations stay within reasonable
    | business parameters. Adjust based on your platform's needs.
    |
    */
    'max_amount' => env('DONATION_MAX_AMOUNT', 100000000),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The primary currency used for donations on the platform.
    |
    */
    'currency' => env('DONATION_CURRENCY', 'IDR'),

    /*
    |--------------------------------------------------------------------------
    | Default Thank You Message
    |--------------------------------------------------------------------------
    |
    | Default message shown to donors after successful donation.
    | Streamers can customize this in their dashboard.
    |
    */
    'default_thank_you_message' => 'Terima kasih atas donasi Anda! Dukungan Anda sangat berarti.',

    /*
    |--------------------------------------------------------------------------
    | Test Alert Configuration
    |--------------------------------------------------------------------------
    |
    | Sample data used for test alerts in streamer dashboard.
    |
    */
    'test_alert' => [
        'names' => [
            'Budi Santoso',
            'Siti Rahayu',
            'Ahmad Fauzi',
            'Dewi Pratiwi',
            'Rizky Maulana',
        ],
        'messages' => [
            'Semangat streamnya!',
            'GG streamer!',
            'Keep it up!',
            'Mantap kali bang!',
            'Halo dari penonton setia!',
        ],
        'emojis' => ['🎉', '💝', '🔥', '👏', '🌟'],
        'amounts' => [5000, 10000, 25000, 50000, 100000],
    ],
];
