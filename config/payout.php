<?php

return [
    'platform_fee_percent' => (int) env('PAYOUT_PLATFORM_FEE_PERCENT', 10),
    'minimum_amount' => (int) env('PAYOUT_MINIMUM_AMOUNT', 50000),
    'automated_disbursement_enabled' => env('PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED', false),
    // Iris requires two separate API keys for the create-then-approve flow:
    // the creator key can create/reject payouts but cannot approve them; the
    // approver key is the only one authorized to call /payouts/approve.
    'iris_api_key' => env('MIDTRANS_IRIS_API_KEY'),
    'iris_approver_api_key' => env('MIDTRANS_IRIS_APPROVER_API_KEY'),
];
