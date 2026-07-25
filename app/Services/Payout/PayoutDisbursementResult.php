<?php

namespace App\Services\Payout;

final class PayoutDisbursementResult
{
    public function __construct(
        public readonly string $status, // 'pending' (manual, no-op) | 'processing' | 'failed'
        public readonly ?string $reference = null,
        public readonly ?string $errorMessage = null,
    ) {}
}
