<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    | Custom Snap transaction expiry. Kept short (default 60 min) rather than
    | Midtrans's own 24h default, since a stream donation is time-sensitive.
    */
    'snap_expiry_minutes' => env('MIDTRANS_SNAP_EXPIRY_MINUTES', 60),
];
