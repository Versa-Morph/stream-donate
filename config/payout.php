<?php

return [
    'platform_fee_percent' => (int) env('PAYOUT_PLATFORM_FEE_PERCENT', 10),
    'minimum_amount' => (int) env('PAYOUT_MINIMUM_AMOUNT', 50000),
    'automated_disbursement_enabled' => env('PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED', false),
    'iris_api_key' => env('MIDTRANS_IRIS_API_KEY'),
];
