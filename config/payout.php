<?php

return [
    'platform_fee_percent' => (int) env('PAYOUT_PLATFORM_FEE_PERCENT', 10),
    'minimum_amount' => (int) env('PAYOUT_MINIMUM_AMOUNT', 50000),
];
