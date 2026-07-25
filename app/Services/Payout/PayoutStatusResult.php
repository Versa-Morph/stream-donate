<?php

namespace App\Services\Payout;

final class PayoutStatusResult
{
    public function __construct(
        public readonly string $status, // 'processing' | 'paid' | 'failed'
    ) {}
}
